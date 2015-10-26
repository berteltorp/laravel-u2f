<?php namespace Certly\U2f\Http\Controllers;

use Auth;
use Input;
use Event;
use Session;
use Redirect;
use App\Event;
use Exception;
use Certly\U2f\U2f as LaravelU2f;
use App\Http\Controllers\Controller;
use Illuminate\Config\Repository as Config;

class U2fController extends Controller
{

    /**
     * @var LaravelU2f
     */
    protected $u2f;

    /**
     * @var Config
     */
    protected  $config;

    /**
     * @param LaravelU2f $u2f
     * @param Config $config
     */
    public function __construct(LaravelU2f $u2f, Config $config)
    {
        $this->u2f = $u2f;
        $this->config = $config;
    }

    /**
     * @author LAHAXE Arnaud
     *
     *
     * @return mixed
     */
    public function registerData()
    {
        list($req, $sigs) = $this->u2f->getRegisterData(Auth::user());
        Event::fire('u2f.register.data', [ 'user' => Auth::user() ]);

        Session::set('u2f.registerData', $req);

        return view($this->config->get('u2f.register.view'))
            ->with('currentKeys', $sigs)
            ->with('registerData', $req);

    }

    /**
     * @author LAHAXE Arnaud
     *
     *
     * @return mixed
     */
    public function register()
    {
        try {
            $key = $this->u2f->doRegister(Auth::user(), Session::get('u2f.registerData'), json_decode(Input::get('register')));
            Event::fire('u2f.register', [ 'u2fKey' => $key, 'user' => Auth::user() ]);
            Session::forget('u2f.registerData');

            if ($this->config->get('u2f.register.postSuccessRedirectRoute')) {

                return Redirect::route($this->config->get('u2f.register.postSuccessRedirectRoute'));
            }
            return redirect('/');
        } catch (Exception $e) {
            return Redirect::route('u2f.register.data');
        }
    }

    /**
     * @author LAHAXE Arnaud
     *
     *
     * @return mixed
     */
    public function authData()
    {
        if($this->u2f->check()) {
            return $this->redirectAfterSuccessAuth();
        }

        $req = $this->u2f->getAuthenticateData(Auth::user());
        Event::fire('u2f.authentication.data', [ 'user' => Auth::user() ]);

        Session::set('u2f.authenticationData', $req);

        return view($this->config->get('u2f.authenticate.view'))
            ->with('authenticationData', $req);
    }

    /**
     * @author LAHAXE Arnaud
     *
     *
     * @return mixed
     */
    public function auth()
    {
        try {
            $key = $this->u2f->doAuthenticate(Auth::user(), Session::get('u2f.authenticationData'), json_decode(Input::get('authentication')));
            Event::fire('u2f.authentication', [ 'u2fKey' => $key, 'user' => Auth::user() ]);
            Session::forget('u2f.authenticationData');

            return $this->redirectAfterSuccessAuth();

        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());

            return Redirect::route('u2f.auth.data');
        }
    }

    protected function redirectAfterSuccessAuth()
    {
        if (strlen($this->config->get('u2f.authenticate.postSuccessRedirectRoute'))) {

            return \Redirect::intended($this->config->get('u2f.authenticate.postSuccessRedirectRoute'));
        } else {
            return \Redirect::intended('/');
        }
    }
}
