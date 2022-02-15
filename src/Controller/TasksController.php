<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Authentication\IdentityInterface;
use Cake\Event\EventInterface;

/**
 * Tasks Controller
 *
 * @property \App\Model\Table\TasksTable $Tasks
 *
 * @method \App\Model\Entity\Task[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TasksController extends AppController
{
    private IdentityInterface $user;

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->user = $this->Authentication->getIdentity();
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
            'conditions' => ['status' => 'O', 'users_id' => $this->user['id']]
        ];
        $tasks = $this->paginate($this->Tasks);

        $this->set(compact('tasks'));
        $this->set('_serialize', 'tasks');
    }

    /**
     * View method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $task = $this->Tasks->get($id, [
            'contain' => ['Users'],
            'conditions' => ['status' => 'O', 'users_id' => $this->user['id']]
        ]);

        $this->set('task', $task);
        $this->set('_serialize', 'task');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $task = $this->Tasks->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['status'] = 'O';
            $data['users_id'] = $this->user['id'];
            $task = $this->Tasks->patchEntity($task, $data);
            if ($this->Tasks->save($task)) {
                $message = 'success';
            } else {
                $message = $task->getErrors();
            }
            $this->set(compact('message'));
            $this->set('_serialize', 'message');
        } else {
            $this->set(compact('task'));
            $this->set('_serialize', 'task');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $task = $this->Tasks->get($id, [
            'conditions' => ['status' => 'O', 'users_id' => $this->user['id']]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData());
            if ($this->Tasks->save($task)) {
                $message = 'success';
            } else {
                $message = $task->getErrors();
            }
            $this->set(compact('message'));
            $this->set('_serialize', 'message');
        } else {
            $this->set(compact('task'));
            $this->set('_serialize', 'task');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $task = $this->Tasks->get($id);
        if ($this->Tasks->delete($task)) {
            $message = 'success';
        } else {
            $message = $task->getErrors();
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }
}
