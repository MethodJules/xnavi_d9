<?php



namespace Drupal\access_management\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AccessManagementForm extends FormBase {

    public function getFormId()
    {
        return 'access_management_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $entity_type_id = 'user';
        $bundle = 'user';
        foreach(\Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle) as $field_name => $field_definition) {
            if(!empty($field_definition->getTargetBundle())) {
                $bundleFields[$entity_type_id][$field_name]['type'] = $field_definition->getType();
                $bundleFields[$entity_type_id][$field_name]['label'] = $field_definition->getLabel();
            }
        }
        $fields = $bundleFields['user'];

        foreach($fields as $field => $value) {
            $options[$field] = $value['label'];
        }

        $form['access_management'] = [
            '#type' => 'checkboxes',
            '#options' => $options,
            '#title' => $this->t('Field Management')
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;

    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $values = $form_state->getValue('access_management');
        $id = 2;

    }
}