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
            /** Get 'cmp' from 'prm[campaign_id]' query parameter. */
            $trackingParameters['cmp'] = $cmp;
        }

        if (
            (null !== $subAffId = $query->get('subAffId'))
            && (preg_match('`^(?<u>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $subAffId, $matches))
        ) {
            /** Get 'u' and 'visit' from 'subAffId' query parameter. */
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['visit'] = $matches['visit'];
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
            && $trackingParameters->has('visit')
        ) {
            $subAffId = sprintf(
                '%s~%s',
                $trackingParameters->get('u'),
                $trackingParameters->get('visit')
            );
        }

        return [
            'prm[campaign_id]' => $trackingParameters->get('cmp'),
            'subAffId' => $subAffId,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'cmp' => $this->defaultCmp,
        ];
    }
}
