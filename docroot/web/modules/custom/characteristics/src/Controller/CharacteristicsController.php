<?php

namespace Drupal\characteristics\Controller;

use Drupal\Core\Controller\ControllerBase;

class CharacteristicsController extends ControllerBase {

    public function start() {

        $user_data = $this->getUserData('member');

        foreach ($user_data as $data) {
            $tasks = $this->getTasks($data['tasks']);
            $background = $this->getTasks($data['background']);
            $users[] =  [
                'name' => $data['name'][0]['value'],
                'aufgaben' => $tasks,
                'image' => 'https://coderdojo-schoeneweide.github.io/images/team/' . strtolower($data['name'][0]['value']) . '.webp',
                'alt' => $this->t('Picture of ' . $data['name'][0]['value']),
                'background' => $background,
                'interests' => 'Musik'
            ];
        }

        return [
            '#theme' => 'characteristics_view',
            '#data' => $users,
            'alt' => 'Bruno',
            '#attached' => [
                'library' => [
                    'characteristics/card-flipper'
                ]
            ]
        ];
    }

    public function getUserData($role) {
        $userStorage = \Drupal::entityTypeManager()->getStorage('user'); //TODO: DI 

        $query = $userStorage->getQuery();
        $uids = $query
            ->condition('status', '1')
            ->condition('roles', $role)
            ->execute();

        $users = $userStorage->loadMultiple($uids);

        foreach($users as $user) {
            $user_data[] = [ 
                'mail' => $user->mail->getValue(),
                'name' => $user->name->getValue(),
                'nickname' => $user->field_nickname->getValue(),
                'tasks' => $user->field_tasks->getValue(),
                'background' => $user->field_background->getValue(),
            ];
        }

        return $user_data;

    }

    public function getTasks($tasks) {
        foreach($tasks as $task) {
            $tid = $task['target_id'];
            $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
            $name = $term->label();
            $names[] = $name;
        }

        if ($names) {
            return implode(', ', $names);
        } else {
            return $this->t('Keine Einträge vorhanden.');
        }
    }
}