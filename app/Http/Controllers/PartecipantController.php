<?php

namespace App\Http\Controllers;

use App\Partecipant;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Storage;

class PartecipantController extends Controller
{
    // Funzione per controllare se è già stato raggiunto il numero massimo di vincitori giornalieri
    public function checkWinnersLimit()
    {
        $maxWinnersPerDay = 5;

        $currentDate = now()->format('Y-m-d');
        $winnersCount = Partecipant::whereDate('created_at', $currentDate)->where('isWinner', true)->count();
        return $winnersCount >= $maxWinnersPerDay;
    }

    //Funzione per controllare se un utente ha già partecipato o meno al contest
    public function checkPartecipant($email){
        $currentDate = now()->format('Y-m-d');
        return Partecipant::where(DB::raw('str_to_date(created_at, "%Y-%m-%d")'),'=', $currentDate)->where('email', $email)->count() > 0;
    }



    public function submit(Request $request)
    {
        // dd($request);

        // Prendo i dati passati dal form
        $name = $request->input('name');
        $lastname = $request->input('lastname');
        $email = $request->input('email');
        $invoice = $request->file('photo');

        // Salvo l'immagine con un nome specifico e lo salvo nello storage pubblico (in caso dovessero essere riutilizzate)
        $fileName = 'at-contest-' .time().$invoice->getClientOriginalName();
        Storage::put('public/'.$fileName,file_get_contents($invoice));
        // dd($filename);

        // Verifico se è stato o meno raggiunto il num max dei vincitori
        $winnersLimitReached = $this->checkWinnersLimit();

            // Se l'utente nel giorno corrente non ha partecipato lo salvo nel db, se no no
            if(!$this->checkPartecipant($email)){
                $partecipant = new Partecipant();
                $partecipant->name = $name;
                $partecipant->last_name = $lastname;
                $partecipant->email = $email;
                $partecipant->invoice = $fileName;

                // Se non è stato raggiunto il limite dei vincitori faccio randomizzare l'esito del contest, se no lo setto a 0 (false) di default
                if(!$winnersLimitReached){
                    $isWinner = (rand(0,1) === 0)? true : false;
                    $partecipant->isWinner = $isWinner;
                    $partecipant->save();
                    if($isWinner){
                        return "<h1>Congratulazioni, $name $lastname! Hai vinto</h1>";
                    }else{
                        return "<h1>Ci dispiace $name $lastname, hai perso! Ritorna domani</h1>";
                    }
                }else{
                    $partecipant->isWinner = false;
                    $partecipant->save();
                    return "<h1>Ci dispiace, il numero massimo di vincitori per oggi è stato raggiunto!<br>Riprova domani</h1>";
                }
            }else{
                return "<h1>Hai già partecipato al concorso di oggi, ritorna domani!</h1>";
            }
    }
}
