<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Cake\Network\Exception\UnauthorizedException;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    
    public function initialize()
    {
        parent::initialize();
        
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
            $this->decoded = JWT::decode($token, Security::getSalt(), ['HS256']);
        }
        
        $this->Auth->allow(['add', 'login']);
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
                $message = $user->errors();
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
                $message = $user->errors();
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
            $message = $user->errors();
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }
    
    public function login()
    {
        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->setUser($user);
            $usuario = [
                'success' => true,
                'data' => [
                    'token' => JWT::encode([
                        'sub' => $user['id'],
                        'exp' => time() + 604800
                    ], Security::getSalt()),
                ],
                'userInfo' => ['username' => $user['username'], 'name' => $user['name']],
                'message' => 'OK'
            ];
            $this->set('usuario', $usuario);
            $this->set('_serialize', 'usuario');
        } else {
            $this->set(['message' => 'Usuário ou senha inválidos', '_serialize' => 'message']);
        }
    }
    
    public function logout()
    {
        $this->Auth->logout();
    }
}
