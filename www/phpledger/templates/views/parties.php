<?php declare(strict_types=1);
$partyInput=($form['input']['action']??'')==='save'?$form['input']:[];
$contactInput=($form['input']['action']??'')==='contact'?$form['input']:[];
$v=$partyInput?:($party?array_merge($party,$party['financial']??[]):['currency'=>$company['currency'],'entity_type'=>'business']);
if ($party || isset($_GET['new']) || $partyInput) { require dirname(__DIR__).'/partials/ui/party-editor.php'; return; }
require dirname(__DIR__).'/partials/ui/party-list.php';
