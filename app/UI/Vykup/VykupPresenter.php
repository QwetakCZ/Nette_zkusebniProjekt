<?php

declare(strict_types=1);

namespace App\UI\Vykup;

use App\Model\DodavateleManager;
use App\Model\VykupManager;
use Contributte\FormsBootstrap\BootstrapForm;
use Mpdf\Mpdf;
use Nette;


final class VykupPresenter extends Nette\Application\UI\Presenter
{
    /** @var VykupManager @inject */
    public $vykupManager;

    /** @var DodavateleManager @inject */
    public $dodavateleManager;
    private string $userId;
    public function startup()
    {
        parent::startup(); // TODO: Change the autogenerated stub

        if(!$this->user->isLoggedIn())
        {
            $this->flashMessage('Pro tuto akci musíte být přihlašen', 'danger');
            $this->redirect('Sign:in');
        }else
        {
            $this->userId = $this->user->identity->getData()['userId'];

        }
    }



    public function renderShow()
    {
        $vykupy = $this->vykupManager->findAllVykupy($this->userId);
        $this->template->vykupy = $vykupy;
        $this->template->dodavatele = $this->dodavateleManager->findAllDodavatele($this->userId)->fetchPairs('id', 'nazev');
    }

    public function actionDelete(int $vykupId):void
    {
        $result = $this->vykupManager->deleteVykup($vykupId, $this->userId);

        if($result)
        {
            // Přesměrování po úspěšném smazani
            $this->flashMessage('Vykup byl úspěšně smazán.', 'success');
            $this->redirect('Vykup:show');
        }else
        {
            // Přesměrování po úspěšném smazani
            $this->flashMessage('Vykup NEBYL úspěšně smazán.', 'danger');
            $this->redirect('Vykup:show');
        }


    }

    public function actionNovyVykup()
    {

    }

    //akce na export vykupu do pdf souboru
    public function actionExportToPdf(int $vykupId, bool $sendByEmail = false):void
    {
        //nacitame si vykup do promene
        $vykup = $this->vykupManager->getVykupById($vykupId, $this->userId);
        //kdyz se nenajde ...
        if (!$vykup) {
            $this->flashMessage('Vykup nebyl nalezen.', 'danger');
            $this->redirect('Vykup:show');
        }
        //potrebuji ziskat jmeno dodavatele z jeho id
        $dodavatelNazev = $this->dodavateleManager->findDodavatelById($vykup->dodavatel_id , $this->userId);
        $template = $this->createTemplate();
        $template->vykup = $vykup;
        //poslani nazvu dodavatele do sablony
        $template->dodavatelNazev = $dodavatelNazev;
        //neco co mi nasloucha a vytvori z toho string
       ob_start();
       $template->setFile(__DIR__ . '/../../../www/templates/pdf_template.latte');
       //zapsani ze sablony do stringu
       $template->render();
       $html = ob_get_clean();

        //vytvoreni instance mpdf pro pdf soubor
       $pdf = new Mpdf();

       $pdf->WriteHTML($html);
        //podminka na zjisteni jestli se ma vykreslit soubor a nebo poslat mailem
       if(!$sendByEmail) {

           $pdf->Output('vykup_' . $vykupId . '.pdf', \Mpdf\Output\Destination::INLINE);

       }else
       {
           // Uložení PDF do dočasného souboru
           $pdfFilePath = __DIR__ . '/../../../www/temp/vykup_' . $vykupId . '.pdf';
           $pdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

           // Odeslání emailu s přílohou
           $mail = new Nette\Mail\Message();
           $mail->setFrom('noreply@domena.cz')
               ->addTo('prijemce@domena.cz')
               ->setSubject('Export vykupu')
               ->setBody('Přikládám PDF export vykupu.')
               ->addAttachment($pdfFilePath);

           $mailer = new Nette\Mail\SendmailMailer();
           $mailer->send($mail);

           // odstraneni souboru z temp
           unlink($pdfFilePath);

           // Přesměrování nebo flash message
           $this->flashMessage('PDF bylo úspěšně odesláno na email.', 'success');
           $this->redirect('Vykup:show');
       }


    }

    public function actionEditace(int $vykupId): void
    {

        // Načtení dodavatele z databáze podle ID a userId, aby každý uživatel viděl jen své dodavatele
        $vykup = $this->vykupManager->getVykupById($vykupId, $this->userId);

        // Pokud dodavatel neexistuje - tak hodime flashmessage s dangerem
        if (!$vykup) {
            $this->flashMessage('Vykup nebyl nalezen.', 'danger');
            $this->redirect('Vykup:show');
        }

        // Poslani vykupu do sablony
        $this->template->vykup = $vykup;

        $this['editForm']->setDefaults([
            'plomba' => $vykup->plomba,
            'vaha' => $vykup->vaha,
            'dodavatel_id' => $vykup->dodavatel_id,
            'prodejniCena' => $vykup->prodejniCena,
        ]);

    }

    protected function createComponentEditForm(): BootstrapForm
    {
        $form = new BootstrapForm();
        $form->addText('plomba', 'Plomba:')
            ->setRequired('Zadejte plombu zvířete.');

        $form->addText('vaha', 'Váha:')
            ->setRequired('Zadejte váhu zvířete.')
            ->addRule(Nette\Application\UI\Form::Float, 'Váha musí být číslo');

        $dodavatele = $this->dodavateleManager->getAll()->where('userId', $this->userId)->fetchPairs('id', 'nazev');

        // Přidání select pole
        $form->addSelect('dodavatel_id', 'Dodavatel:', $dodavatele)
            ->setPrompt('Vyberte dodavatele')
            ->setRequired('Vyberte dodavatele.');

        $form->addText('prodejniCena', 'Prodejní cena:')
            ->setRequired('Zadejte prodejní cenu.')
            ->addRule(Nette\Application\UI\Form::Float, 'Cena musí být číslo');

        $form->addSubmit('submit', 'Uložit změny');

        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    public function editFormSucceeded(BootstrapForm $form, array $values): void
    {
        // Získání ID dodavatele z parametru URL

        $vykupId = (int) $this->getParameter('vykupId');

        if($vykupId) {
            // Uložení upravených údajů do databáze
            $this->vykupManager->updateVykup($vykupId, $this->userId, $values);

            // Přesměrování po úspěšné editaci
            $this->flashMessage('Výkup byl úspěšně upraven.', 'success');
            $this->redirect('Vykup:show');
        }else{
            $data = [
                'plomba' => $values['plomba'],
                'vaha' => $values['vaha'],
                'dodavatel_id' => $values['dodavatel_id'],
                'userId' => $this->userId,
                'prodejniCena'=> $values['prodejniCena']
            ];
            $this->vykupManager->createVykup($data);

            $this->flashMessage('Nový výkup úspěšně vytvořen', 'success');
            $this->redirect('Vykup:show');
        }
    }




}
