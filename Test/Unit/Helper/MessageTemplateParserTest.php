<?php
/**
 * Pmclain_Twilio extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GPL v3 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl.txt
 *
 * @category       Pmclain
 * @package        Twilio
 * @copyright      Copyright (c) 2017
 * @license        https://www.gnu.org/licenses/gpl.txt GPL v3 License
 */

namespace Pmclain\Twilio\Test\Unit\Helper;

use Pmclain\Twilio\Helper\MessageTemplateParser;

/** @codeCoverageIgnore */
class MessageTemplateParserTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var \Pmclain\Twilio\Helper\MessageTemplateParser
   */
  protected $messageTemplateParser;

  protected function setUp() {
    $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    $this->messageTemplateParser = $objectManager->getObject(MessageTemplateParser::class);
  }

  /**
   * @dataProvider testParseTemplateDataProvider
   */
  public function testParseTemplate($template, $vars, $expectedResult) {
    $this->assertEquals(
      $this->messageTemplateParser->parseTemplate($template, $vars),
      $expectedResult
    );
  }

  public function testParseTemplateDataProvider() {
    return [
      //test with two string variables that both exist
      [
        'Hi {{customer.firstname}}, Thanks for your order. Your order number is {{order.increment_id}}.',
        [
          'customer.firstname' => 'Bob',
          'order.increment_id' => '000000023'
        ],
        'Hi Bob, Thanks for your order. Your order number is 000000023.'
      ],
      //test with placeholder not included in variables
      [
        'Hi {{customer.firstname}}, Thanks for your order. Your order number is {{order.increment_id}}.',
        [
          'customer.firstname' => 'Bob'
        ],
        'Hi Bob, Thanks for your order. Your order number is .'
      ],
      //test with no placeholders
      [
        'Hey there! Thanks for placing and order. We\'ll send you a tracking number as soon as it ships.',
        [],
        'Hey there! Thanks for placing and order. We\'ll send you a tracking number as soon as it ships.'
      ],
      //test with array as variable
      [
        'Order {{order.increment_id}} has shipped. Your tracking number is {{shipment.trackingnumber}}.',
        [
          'order.increment_id' => '000000023',
          'shipment.trackingnumber' => ['tracknumber1', 'tracknumber2']
        ],
        'Order 000000023 has shipped. Your tracking number is tracknumber1, tracknumber2.'
      ]
    ];
  }
}