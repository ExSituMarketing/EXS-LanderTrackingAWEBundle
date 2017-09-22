<?php

namespace EXS\LanderTrackingAWEBundle\Tests\Service\Formatter;

use EXS\LanderTrackingAWEBundle\Service\TrackingParameterManager\AweTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class AweTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('prm[campaign_id]')->willReturn(123)->shouldBeCalledTimes(1);
        $query->get('subAffId')->willReturn('UUID987654321~5')->shouldBeCalledTimes(1);

        $manager = new AweTrackingParameterManager(1);

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag();

        $manager = new AweTrackingParameterManager(1);

        $result = $manager->format($trackingParameters);

        $this->assertCount(2, $result);

        $this->assertArrayHasKey('prm[campaign_id]', $result);
        $this->assertNull($result['prm[campaign_id]']);

        $this->assertArrayHasKey('subAffId', $result);
        $this->assertNull($result['subAffId']);
    }

    public function testFormatWithProperParameters()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'u' => 'UUID987654321',
            'visit' => 5,
            'foreign_id' => 456,
        ]);

        $manager = new AweTrackingParameterManager(1);

        $result = $manager->format($trackingParameters);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('prm[campaign_id]', $result);
        $this->assertEquals(123, $result['prm[campaign_id]']);
        $this->assertArrayHasKey('subAffId', $result);
        $this->assertEquals('UUID987654321~5', $result['subAffId']);
    }

    public function testInitiailise()
    {
        $manager = new AweTrackingParameterManager(1);

        $result = $manager->initialize();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(1, $result['cmp']);
    }
}
