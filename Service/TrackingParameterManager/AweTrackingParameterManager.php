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
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (null !== $cmp = $request->query->get('prm[campaign_id]')) {
            /** Get 'cmp' from 'prm[campaign_id]' query parameter. */
            $trackingParameters['cmp'] = $cmp;
        } elseif ($request->cookies->has('cmp')) {
            $trackingParameters['cmp'] = $request->cookies->get('cmp');
        }

        if (
            (null !== $subAffId = $request->query->get('subAffId'))
            && (preg_match('`^([a-z0-9]+)~([a-z0-9]+)$`i', $subAffId, $matches))
        ) {
            /** Get 'exid' and 'visit' from 'subAffId' query parameter. */
            $trackingParameters['exid'] = $matches[1];
            $trackingParameters['visit'] = $matches[2];
        } elseif (
            ($request->cookies->has('exid'))
            && ($request->cookies->has('visit'))
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
            'prm[campaign_id]' => $trackingParameters->get('cmp'),
            'subAffId' => $subAffId,
        ];
    }
}
