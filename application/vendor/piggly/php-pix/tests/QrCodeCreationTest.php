<?php
namespace Piggly\Tests\Pix;

use PHPUnit\Framework\TestCase;
use Piggly\Pix\Parser;
use Piggly\Pix\StaticPayload;

/**
 * @coversDefaultClass \Piggly\Pix\StaticPayload
 */
class QrCodeCreationTest extends TestCase
{
	/**
	 * Assert if $qrcode is a base64 string.
	 * 
	 * Anytime it runs will create 100 random pix
	 * codes. It must assert all anytime.
	 *
	 * @covers Reader::getQRCode
	 * @dataProvider dataPix
	 * @test Expecting positive assertion.
	 * @param string $qrcode Pix data type.
	 * @return boolean
	 */
	public function isMatching ( string $qrcode )
	{ $this->assertNotFalse(strpos($qrcode, 'data:image/svg+xml;base64')); }

	/**
	 * A list with random pix to validate.
	 * Provider to isMatching() method.
	 * Generated by fakerphp.
	 * @return array
	 */
	public function dataPix () : array
	{
		$arr = [];

		$faker = \Faker\Factory::create('pt_BR');

		for ( $i = 0; $i < 20; $i++ )
		{
			// Key
			$key = $this->getRandomKey($faker);

			// Payload
			$pix = new StaticPayload();
			$pix
				->setAmount($faker->randomFloat(2))
				->setPixKey($key['KEY_TYPE'], $key['KEY_VALUE'])
				->setMerchantCity($faker->city())
				->setMerchantName($faker->firstName().' '.$faker->lastName());

			// Description
			if ( $faker->boolean() )
			{ $pix->setDescription($faker->words(3, true)); }

			// Tid
			if ( $faker->boolean() )
			{ $pix->setTid($faker->regexify('[a-zA-Z0-9]{25}')); }

			// Description
			if ( $faker->boolean() )
			{ $pix->setPostalCode($faker->postcode()); }

			$arr[] = [$pix->getQRCode()];
		}

		return $arr;
	}
	
	/**
	 * Get random pix key.
	 * 
	 * @param Faker $faker
	 * @return array
	 */
	protected function getRandomKey ( $faker ) : array 
	{
		$num = $faker->numberBetween(0, 4);

		switch ( $num )
		{
			case 0:
				return ['KEY_TYPE' => Parser::KEY_TYPE_DOCUMENT, 'KEY_VALUE' => $faker->cnpj()];
			case 1:
				return ['KEY_TYPE' => Parser::KEY_TYPE_DOCUMENT, 'KEY_VALUE' => $faker->cpf()];
			case 2:
				return ['KEY_TYPE' => Parser::KEY_TYPE_EMAIL, 'KEY_VALUE' => $faker->email()];
			case 3:
				return ['KEY_TYPE' => Parser::KEY_TYPE_PHONE, 'KEY_VALUE' => $faker->phoneNumber()];
			case 4:
				return ['KEY_TYPE' => Parser::KEY_TYPE_RANDOM, 'KEY_VALUE' => $this->genUuid()];
		}
	}
	
	/**
	 * Generate random uuidv4.
	 *
	 * @return void
	 */
	protected function genUuid ()
	{
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
}