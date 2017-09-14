<?php

namespace EXS\LanderTrackingAWEBundle\Tests\Service\Formatter;

use EXS\LanderTrackingAWEBundle\Service\TrackingParameterManager\AweTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class AweTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('prm[campaign_id]')->willReturn(null)->shouldBeCalledTimes(1);
        $query->get('subAffId')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(false)->shouldBeCalledTimes(1);
        $cookies->has('exid')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new AweTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertEmpty($result);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('prm[campaign_id]')->willReturn(null)->shouldBeCalledTimes(1);
        $query->get('subAffId')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('cmp')->willReturn(123)->shouldBeCalledTimes(1);

        $cookies->has('exid')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->has('visit')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('exid')->willReturn('UUID987654321')->shouldBeCalledTimes(1);
        $cookies->get('visit')->willReturn(5)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new AweTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('prm[campaign_id]')->willReturn(123)->shouldBeCalledTimes(1);
        $query->get('subAffId')->willReturn('UUID987654321~5')->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new AweTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag();

        $manager = new AweTrackingParameterManager();

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
            'exid' => 'UUID987654321',
            'visit' => 5,
            'foreign_id' => 456,
        ]);

        $manager = new AweTrackingParameterManager();

        $result = $manager->format($trackingParameters);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('prm[campaign_id]', $result);
        $this->assertEquals(123, $result['prm[campaign_id]']);
        $this->assertArrayHasKey('subAffId', $result);
        $this->assertEquals('UUID987654321~5', $result['subAffId']);
    }
}
