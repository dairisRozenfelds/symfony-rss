<?php

namespace App\Tests\RssParsers;

use App\Tools\RssParsers\TheRegisterParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Tests\TestHelper;

class TheRegisterParserTest extends KernelTestCase
{
    public function testWordCounter()
    {
        $testCase = [
            'input' => [
                [
                    'content_summary' => 'Fluent, fluent everywhere but not a patch that works Good news everyone! While Microsoft seems unable to deliver a patch that won\'t leave Windows 10 in a parlous state for some users, it does possess the will to fiddle with the icons. Again.…'
                ],
                [
                    'content_summary' => 'I\'ll take a Big Mac, large fries and... um, are you OK? Bork!Bork!Bork!  There is a saying about networking fails: "It\'s not DNS. It can\'t be DNS. It was DNS." So far for The Register\'s column of retail calamity, it\'s McDonald\'s. It\'s nearly always McDonald\'s.…'
                ]
            ],
            'output' => [
                [
                    'word' => 'dns',
                    'word_count' => 3
                ],
                [
                    'word' => 'it\'s',
                    'word_count' => 3
                ],
                [
                    'word' => 'bork',
                    'word_count' => 3
                ],
                [
                    'word' => 'fluent',
                    'word_count' => 2
                ],
                [
                    'word' => 'mcdonald\'s',
                    'word_count' => 2
                ],
                [
                    'word' => 'patch',
                    'word_count' => 2
                ],
                [
                    'word' => 'networking',
                    'word_count' => 1
                ],
                [
                    'word' => 'large',
                    'word_count' => 1
                ],
                [
                    'word' => 'fries',
                    'word_count' => 1
                ],
                [
                    'word' => 'um',
                    'word_count' => 1
                ]
            ]
        ];

        self::bootKernel();
        /** @var TheRegisterParser $parser */
        $parser = self::$container->get('App\Tools\RssParsers\TheRegisterParser');

        $result = TestHelper::invokeMethod($parser, 'getWordOccurrences', [$testCase['input']]);

        $this->assertEquals($testCase['output'], $result);
    }
}
