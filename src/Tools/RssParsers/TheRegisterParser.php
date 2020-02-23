<?php

namespace App\Tools\RssParsers;

use App\Entity\EnglishCommonWord;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use function simplexml_load_file;

class TheRegisterParser
{
    /**
     * @var string
     */
    protected $url = 'https://www.theregister.co.uk/software/headlines.atom';

    /**
     * @var int
     */
    protected $topCommonWordReturnCount = 10;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TheRegisterParser constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function parse(): array
    {
        $feed = simplexml_load_file($this->url);
        $result = [];

        if ($feed) {
            foreach ($feed->entry as $entry) {
                $entryArray['id'] = (string)$entry->id;
                $entryArray['updated_at'] = DateTime::createFromFormat(DateTime::ATOM, (string)$entry->updated)
                    ->format('Y-m-d H:i:s');
                $entryArray['author_name'] = (string)($entry->author->name);
                $entryArray['title'] = (string)$entry->title;
                $entryArray['content_summary'] = strip_tags((string)$entry->summary);
                $entryArray['link'] = (string)$entry->link['href'];

                $result['entries'][] = $entryArray;
            }
        }

        $result['common_words'] = $this->getWordOccurrences($result['entries']);

        return $result;
    }

    /**
     * Counts and returns the most common words from the provided string in the format:
     * [
     *     [
     *         'word' => WORD,
     *         'count' => COUNT
     *     ]
     * ]
     *
     * @param $result
     * @return array
     */
    protected function getWordOccurrences($result)
    {
        $wordCount = [];

        foreach ($result as $entry) {
            $wordArray = preg_split('/(\s)|([.,\/#!$%\^&\*;:{}=\-_`~()"])/', $entry['content_summary'], -1, PREG_SPLIT_NO_EMPTY);;

            foreach ($wordArray as $word) {
                $word = preg_replace('/[^A-Za-z\-\']/', '', $word);

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

        // Exclude the most common words from the word count array
        foreach ($mostCommonWords as $commonWord) {
            if (array_key_exists($commonWord->getWord(), $wordCount)) {
                unset($wordCount[$commonWord->getWord()]);
            }
        }

        arsort($wordCount);

        $result = [];

        foreach (array_slice($wordCount, 0, $this->topCommonWordReturnCount, true) as $word => $count) {
            $result[] = [
                'word' => $word,
                'word_count' => $count
            ];
        }

        return $result;
    }

    /**
     * @return EnglishCommonWord[]
     */
    protected function getMostCommonWords()
    {
        return $this->entityManager->getRepository(EnglishCommonWord::class)->findAll();
    }
}
