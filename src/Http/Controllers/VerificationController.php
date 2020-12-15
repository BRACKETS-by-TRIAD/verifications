<?php

namespace Brackets\Verifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Verification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * @var Verification
     */
    private $verification;

    public function showVerificationForm($redirectTo)
    {
        return view("verification", compact($redirectTo));
    }

    /**
     * @param Request $request
     * @param Verifiable $verifiable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, Verifiable $verifiable)
    {
        //TODO: flash messages
        return $this->verification->verifyCode($verifiable, $request->get('code'))
               ? redirect()->route($request->get('redirectTo'))
               : redirect()->back();
    }
}

/**
 * Use case: nastavenie 2FA v profile
 *
 * v profile zapnut 2FA (checkbox) + formular na zadanie telefonneho cisla + submit tohto formularu (+ moznost zmenit telefonne cislo a tiez moznost vygenerovat novy kod + error response) + success response
 */


/**
 * Use case: 2FA login
 *
 * pri postLogin potrebujeme zistit, ci je zapnuta 2FA, ak ano, tak automaticky posielame kod + proceds pokracuje ako vyssie ale s tym ze konci inde
 * 2FA auth sa da nastavit ze je required pre cely system vs. ze je disabled pre cely sustem vs. ze user si sam voli
 *
 * ak je per system tak v postLogin treba zavoalt generateCodeAndSend()
 * ak je per usr, tak v postLogin treba spravit check if ($user->2FA_enabled) { generateCodeAndSend(); }
 */


/**
 * Use case: nejaka ina akcia, ktora ma byt overena SMS/email kodom (napr. podpisanie zmluvy, zmena hesla)
 *
 * v danom controlleri portebujeme zavolat nieco, co spusti proces
 */
