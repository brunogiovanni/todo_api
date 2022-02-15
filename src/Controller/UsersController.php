<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Http\Cookie\Cookie;
use Firebase\JWT\JWT;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['add', 'login']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', 'users');
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
        $this->set('_serialize', 'user');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $message = 'success';
            } else {
                $message = $user->getErrors();
            }
            $this->set(compact('message'));
            $this->set('_serialize', 'message');
        } else {
            $this->set(compact('user'));
            $this->set('_serialize', 'user');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $message = 'success';
            } else {
                $message = $user->getErrors();
            }
            $this->set(compact('message'));
            $this->set('_serialize', 'message');
        } else {
            $this->set(compact('user'));
            $this->set('_serialize', 'user');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $message = 'success';
        } else {
            $message = $user->getErrors();
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }

    public function login()
    {
        if ($this->request->is('options')) {
            return $this->response->withType('application/json');
        }
        $response = new Response();
        $result = $this->Authentication->getResult();
        $cookie = new Cookie('JwtCookie', '');
        if ($result->isValid()) {
            $privateKey = file_get_contents(CONFIG . 'jwt.key');
            $user = $result->getData();
            $payload = [
                'iss' => 'myapp',
                'sub' => $user->id,
                'exp' => time() + 60,
            ];
            $json = [
                'token' => JWT::encode($payload, $privateKey, 'RS256'),
            ];
            $status = 200;
            $cookie = new Cookie('JwtCookie', $json['token'], null, null, null, false, true, null);
        } else {
            $status = 401;
            $json = ['mensagem' => 'Não autorizado'];
        }

        return $response->withType('application/json')
            ->withStringBody(json_encode($json))
            ->withStatus($status)
            ->withCookie($cookie);
    }

    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $this->Authentication->logout();
            $response = new Response();

            return $response->withType('application/json')->withStringBody(json_encode(['mensagem' => 'sucesso']));
        }
    }
}
