<?php

namespace EXS\LanderTrackingAWEBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterInitializerInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterQueryExtracterInterface;

/**
 * Class AweTrackingParameterManager
 *
 * @package EXS\LanderTrackingAWEBundle\Service\TrackingParameterManager
 */
class AweTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface, TrackingParameterInitializerInterface
{
    /**
     * @var int
     */
    private $defaultCmp;

    /**
     * AweTrackingParameterManager constructor.
     *
     * @param $defaultCmp
     */
    public function __construct($defaultCmp)
    {
        $this->defaultCmp = $defaultCmp;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (null !== $cmp = $query->get('prm[campaign_id]')) {
            /** Get 'c' from 'prm[campaign_id]' query parameter. */
            $trackingParameters['c'] = $cmp;
        }

        if (
            (null !== $subAffId = $query->get('subAffId'))
            && (preg_match('`^(?<u>[a-z0-9]+)~(?<v>[a-z0-9]+)$`i', $subAffId, $matches))
        ) {
            /** Get 'u' and 'v' from 'subAffId' query parameter. */
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['v'] = $matches['v'];
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $subAffId = null;
        if (
            $trackingParameters->has('u')
            && $trackingParameters->has('v')
        ) {
            $subAffId = sprintf(
                '%s~%s',
                $trackingParameters->get('u'),
                $trackingParameters->get('v')
            );
        }

        return [
            'prm[campaign_id]' => $trackingParameters->get('c'),
            'subAffId' => $subAffId,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'c' => $this->defaultCmp,
        ];
    }
}
