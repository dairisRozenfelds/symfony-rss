<?php

namespace App\Tools\RssParsers;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use function simplexml_load_file;

class TheRegisterParser
{
    /**
     * @var string
     */
    protected $url = 'https://www.theregister.co.uk/software/headlines.atom';

    /**
     * Name for the csv file containing the most common words located in project's "assets/csv" folder
     * @var string
     */
    protected $commonWordCsvFilename = 'most_common_words.csv';

    /**
     * @var int
     */
    protected $topCommonWordReturnCount = 10;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * TheRegisterParser constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     *
     */
    public function parse(): array
    {
        $feed = simplexml_load_file($this->url);
        $result = [];

        if ($feed) {
            foreach ($feed->entry as $entry) {
                $entryArray['id'] = (string)$entry->id;
                $entryArray['updated'] = (string)$entry->updated;
                $entryArray['author_name'] = (string)($entry->author->name);
                $entryArray['title'] = (string)$entry->title;
                $entryArray['summary'] = (string)$entry->summary;

                $result['entries'][] = $entryArray;
            }
        }

        $result['common_words'] = $this->getWordOccurrences($result['entries']);

        return $result;
    }

    /**
     * @param $result
     * @return array
     */
    protected function getWordOccurrences($result)
    {
        $wordCount = [];

        foreach ($result as $entry) {
            $wordArray = explode(' ', trim(strip_tags($entry['summary'])));

            foreach ($wordArray as $word) {
                $word = preg_replace('/[^A-Za-z\-]/', '', $word);

                if ($word === '') {
                    continue;
                }

                $word = strtolower($word);

                if (array_key_exists($word, $wordCount)) {
                    $wordCount[$word]++;
                } else {
                    $wordCount[$word] = 1;
                }
            }
        }

        $mostCommonWords = $this->getMostCommonWords();

        foreach ($mostCommonWords as $commonWord) {
            if (array_key_exists($commonWord, $wordCount)) {
                unset($wordCount[$commonWord]);
            }
        }

        arsort($wordCount);

        return array_slice($wordCount, 0, 10, true);
    }

    /**
     * @return array
     */
    protected function getMostCommonWords()
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder([CsvEncoder::NO_HEADERS_KEY => true])]);
        $rawCsvValues = $serializer->decode(
            file_get_contents($this->kernel->getProjectDir() . '/assets/csv/' . $this->commonWordCsvFilename),
            'csv'
        );

        $formattedArray = [];

        foreach ($rawCsvValues as $csvValue) {
            if (isset($csvValue[0])) {
                $formattedArray[] = strtolower(trim($csvValue[0]));
            }
        }

        return $formattedArray;
    }
}
