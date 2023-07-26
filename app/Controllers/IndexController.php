<?php
namespace App\Controllers;

use App\Models\clientes;

use Illuminate\Database\Capsule\Manager as Capsule;      //? conexion con la base de datos usando Query Builder
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;


class IndexController extends CoreController{
    public function indexAction(){
        if( isset($_SESSION['cedula']) )
            return new RedirectResponse('/dashboard');
        else
            return $this->renderHTML('index.twig');
    }

    public function postLoginAction(ServerRequest $request){
        $responseMessage = null;    //var para recuperar los mesajes q suceda durante la ejecucion

        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();
            print_r($postData);


            $validator = v::key('cedula', v::stringType()->noWhitespace()->notEmpty())
            ->key('password', v::stringType()->notEmpty()->noWhitespace());

            try {
                $validator->assert($postData);   //? validando

                $cliente = new clientes();
                $existe = $cliente
                                ->where("cedula", $postData['cedula'])
                                ->first();
                if( $existe )
                    if ( password_verify( $postData['password'], $existe->password) ){
                        $_SESSION['nombres'] = $existe->nombres;
                        $_SESSION['cedula'] = $existe->cedula;
                        $_SESSION['id_cliente'] = $existe->id_cliente;

                        return new RedirectResponse('/dashboard');
                    }else{
                        $responseMessage = 'Credenciales incorrectas o el usuario no existe';
                    }
                else
                    $responseMessage = 'Credenciales incorrectas o el usuario no existe';
                            
            } catch (\Exception $e) {
                $responseMessage = 'Credenciales incorrectas o el usuario no existe';
                // $responseMessage = $e->getMessage();
            }
        }

        //mensaje de retorno a la misma vista con un alert
        return $this->renderHTML('index.twig', [
            'responseMessage' => assetsControler::sweetAlert($responseMessage, 'error')
        ]);
    }

    public function getFormSignupAction(){
        if( isset($_SESSION['cedula']) )
            return new RedirectResponse('/dashboard');
        else
            return $this->renderHTML('signup.twig');
    }

    public function postSignupAction(ServerRequest $request){
        $responseMessage = null;    //var para recuperar los mesajes q suceda durante la ejecucion

        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();

            $validator = v::key('password', v::stringType()->noWhitespace()->notEmpty())
            ->key('cedula', v::stringType()->notEmpty()->noWhitespace())
            ->key('telefono', v::stringType()->notEmpty()->noWhitespace())
            ->key('nombres', v::stringType()->notEmpty());
            
            try {
                $validator->assert($postData);

                Capsule::beginTransaction();
                
                $cliente = new clientes();
                $cliente->cedula = $postData['cedula'];
                $cliente->nombres = $postData['nombres'];
                $cliente->telefono = $postData['telefono'];
                $postData['password'] = password_hash($postData['password'], PASSWORD_DEFAULT );
                $cliente->password = $postData['password'];
                
                $cliente->save();
                Capsule::commit();

                $lastId = $cliente->id;
                $responseMessage = 'Se ha guardado con éxito';

                $_SESSION['nombres'] = $cliente->nombres;
                $_SESSION['cedula'] = $cliente->cedula;
                $_SESSION['id_cliente'] = $lastId;

                return new RedirectResponse('/dashboard');
            } catch (\Exception $e) {
                // $responseMessage = $e->getMessage();
                Capsule::rollback();
                $responseMessage = $e->getCode();
                if ($responseMessage === '23000') 
                    $responseMessage = 'El usuario ya esta registrado';
                else
                    $responseMessage = 'Ha ocurrido un error! Informe a soporte';

            }
        }

        return $this->renderHTML('signup.twig', [
            'responseMessage' => assetsControler::sweetAlert($responseMessage, 'error')
        ]);
    }

    public function getLogoutAction(){
        unset($_SESSION['ruc']);
        session_destroy();
        return new RedirectResponse('/');
    }
}

