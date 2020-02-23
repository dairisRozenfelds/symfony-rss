<?php

namespace App\DataFixtures;

use App\Entity\EnglishCommonWord;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EnglishCommonWordFixtures extends Fixture
{
    /**
     * Name of the CSV file that will be used as a source for the most common words
     *
     * @var string
     */
    private $commonWordCsvFilename = 'english_most_common_words.csv';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder([CsvEncoder::NO_HEADERS_KEY => true])]);
        $rawCsvValues = $serializer->decode(
            file_get_contents(__DIR__ . '/data/csv/' . $this->commonWordCsvFilename),
            'csv'
        );

        $rank = 1;

        foreach ($rawCsvValues as $csvValue) {
            if (!empty($csvValue[0])) {
                $commonWord = new EnglishCommonWord();
                $commonWord->setWord(strtolower(trim($csvValue[0])));
                $commonWord->setRank($rank++);

                $manager->persist($commonWord);
            }
        }

        $manager->flush();
    }
}
