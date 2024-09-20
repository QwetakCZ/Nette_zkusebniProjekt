<?php

declare(strict_types=1);

namespace App\UI\Sign;



use App\Model\UserManager;
use App\UI\BasePresenter;
use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;
use App\Model\Authenticator;



final class SignPresenter extends BasePresenter
{
    private UserManager $userManager;
    private Authenticator $authenticator;
    private string $storeRequestId;

    public function __construct(
        UserManager $userManager,
        Authenticator $authenticator
    )

    {
        $this->userManager = $userManager;
        $this->authenticator = $authenticator;
    }


    public function actionIn($storeRequestId = '')
    {
        $this->storeRequestId = $storeRequestId;
    }

    public function createComponentSignInForm(): Form
    {
        $form = new BootstrapForm();
        $form->addText('email', 'Jméno:')
            ->setRequired('Zadejte prosím jméno');
        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte prosím heslo');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'Prihlasit'];
        return $form;
    }

    public function createComponentSignUpForm(): Form
    {
        $form = new Form();
        $form->addText('username', 'Zadejte uživatelské jméno:')
            ->setRequired('Zadejte své uživatelské jméno.');
        $form->addEmail('email', 'Zadejte svůj email:')
            ->setRequired('Zadejte svůj email.');
        $form->addPassword('password', 'Zadejte heslo:')
            ->setRequired('Zadejte své heslo.');
        $form->addPassword('passwordConfirm', 'Potvrďte heslo:')
            ->setRequired('Potvrďte své heslo.')
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password']);
        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = [$this, 'Registrace'];
        return $form;
    }

    public function Prihlasit(Form $form, \stdClass $data): void
    {
        try {
            // Pokus o přihlášení
            $this->user->login($data->email, $data->password);

            $identity = $this->authenticator->authenticate($data->email, $data->password);
            $this->getUser()->login($identity);
            $this->flashMessage('Přihlášení proběhlo úspěšně', 'success');
            $this->redirect('Home:');
        } catch (AuthenticationException $e) {
            // Zpracování chyby při přihlášení
            $form->addError('Neplatné přihlašovací údaje.');
        }
    }

    public function Registrace(Form $form, \stdClass $data): void
    {
        try {

            $this->userManager->add($data->username, $data->email, $data->password);
            $this->flashMessage('Registrace proběhla úspěšně.');
            $this->redirect('Sign:in'); //
        } catch (DuplicateNameException $e) {

            $form->addError('Uživatelské jméno nebo email již existuje.');
        }
    }

    public function actionOut()
    {
        $this->user->logout(true);
        $this->flashMessage('Odhlašení proběhlo úspěšně', 'warning');
        $this->redirect('Home:');
    }








}
