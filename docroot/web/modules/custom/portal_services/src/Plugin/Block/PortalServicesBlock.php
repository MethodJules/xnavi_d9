<?php

namespace Drupal\portal_services\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Portal Services' Block
 * 
 * @Block(
 *   id = "portal_services_block",
 *   admin_label = @Translation("Portal Services Block"),
 *   category = @Translation ("Portal Services")
 * )
 */
class PortalServicesBlock extends BlockBase {

    public function build() {
        return [
            '#theme' => 'portal_services',
            '#data' => ['Portal_1' => 'Urban Garden', 'Portal_2' => 'Coder Dojo'],
            '#attached' => [
                'library' => [
                    'portal_services/portal-services'
                ]
            ]
        ];
    }
}