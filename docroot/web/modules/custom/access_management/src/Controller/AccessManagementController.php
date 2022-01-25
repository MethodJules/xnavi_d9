<?php

namespace Drupal\access_management\Controller;

class AccessManagementController {
    public function content() {
        $entity_type_id = 'user';
        $bundle = 'user';
        foreach(\Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle) as $field_name => $field_definition) {
            if(!empty($field_definition->getTargetBundle())) {
                $bundleFields[$entity_type_id][$field_name]['type'] = $field_definition->getType();
                $bundleFields[$entity_type_id][$field_name]['label'] = $field_definition->getLabel();
            }
        }
        $fields = $bundleFields['user'];
        
        $id =2;
        
        
        return ['#markup' => '<p>Access Management</p>'];
    }
}