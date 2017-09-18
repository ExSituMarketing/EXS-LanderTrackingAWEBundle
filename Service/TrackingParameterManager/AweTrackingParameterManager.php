<?php

namespace EXS\LanderTrackingAWEBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterExtracterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;

/**
 * Class AweTrackingParameterManager
 *
 * @package EXS\LanderTrackingAWEBundle\Service\TrackingParameterManager
 */
class AweTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
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
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (null !== $cmp = $request->query->get('prm[campaign_id]')) {
            /** Get 'cmp' from 'prm[campaign_id]' query parameter. */
            $trackingParameters['cmp'] = $cmp;
        } else {
            $trackingParameters['cmp'] = $request->cookies->get('cmp', $this->defaultCmp);
        }

        if (
            (null !== $subAffId = $request->query->get('subAffId'))
            && (preg_match('`^(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $subAffId, $matches))
        ) {
            /** Get 'exid' and 'visit' from 'subAffId' query parameter. */
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['visit'] = $matches['visit'];
        } elseif (
            $request->cookies->has('exid')
            && $request->cookies->has('visit')
        ) {
            $trackingParameters['exid'] = $request->cookies->get('exid');
            $trackingParameters['visit'] = $request->cookies->get('visit');
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
            $trackingParameters->has('exid')
            && $trackingParameters->has('visit')
        ) {
            $subAffId = sprintf(
                '%s~%s',
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit')
            );
        }

        return [
            'prm[campaign_id]' => $trackingParameters->get('cmp', $this->defaultCmp),
            'subAffId' => $subAffId,
        ];
    }
}
