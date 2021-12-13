<?php
declare(strict_types=1);
require "autoload.php";

$auth = new SecureUserAuthentication();
if(!(SecureUserAuthentication::isUserConnected() || $auth->getUser()->isVeto() || $auth->getUser()->isAdmin()))
    header("Location: connexion.php");

$req = MyPDO::getInstance()->prepare(<<<SQL
            SELECT *
            FROM Users
            WHERE userId = ?;
        SQL
);
$req->execute([$_GET['userId']]);
$data = $req->fetch();

$user = new User($data);
$animals = $user->getAnimals();

$webPage = new WebPage("Fiche Client");
$webPage->appendCss(<<<CSS
.borderR {
    border-right: 2px solid #828282;
}

table {
  table-layout: fixed;
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 10px;
}

th {
font-weight: bold;
}

.tabAnimaux th[scope=row], .tabClient th[scope=row] {
    border-right: 2px solid #828282;
}

th[scope=col] {
border-bottom: 2px solid #828282;
}

.tabAnimaux th{
    font-size: 20px;
    color:#02897A;
    width: 50%;
}

.tabVaccins th{
    width: 50%;
}

.tabVaccins td{
    font-weight: bold;
    color: #02897A;
}

.buttonNewPresta{
    font-weight: bold;
    justify-content: center;
    letter-spacing: 0.02em;
    background-color: #02897A;
    color: white;
    border-radius: 10px;
    transition: 0.2s background-color ease-in-out;
    padding: 13px;
    width: 100%;
    border:none;
}

.buttonNewPresta:hover {
    background-color: #055945;
}
CSS);


//INFORMATION ANIMAUX DU CLIENT ET INSERTION DANS UN TABLEAU//
$tabAnimaux="";
foreach ($animals as $animal) {
    $tabAnimaux .= <<< HTML
<tr>
    <td class="d-flex flex-row borderR" style="justify-content: space-between;">
        <div>{$animal->getName()}</div>
HTML;
    if ($animal->getThreatId() == 1){
        $tabAnimaux .= '<div style="height: 23px; width: 23px; background-color: limegreen; border: none; border-radius: 50%;"></div>';
    }
    if($animal->getThreatId() == 2){
        $tabAnimaux .= '<div style="height: 23px; width: 23px; background-color: orange; border: none; border-radius: 50%;"></div>';
    } elseif ($animal->getThreatId() == 3 ) {
        $tabAnimaux .= '<div style="height: 23px; width: 23px; background-color: red; border: none; border-radius: 50%;"></div>';
    }

    $tabAnimaux .= <<< HTML
    </td>
    <td class="borderR">{$animal->getSpecieName()}</td>
    <td>{$animal->getNameRace()}</td>
</tr>
HTML;
}

$animalSelect = $animals[0];

$html = <<< HTML
<div class="d-flex flex-column" style="padding-top: 100px;">
    <div class="d-flex justify-content-center" style="background-color: #262626; border-radius: 10px; width: 45%; align-self: center;">
        <h3 style="font-weight: bold;background-color: #262626; color: white; font-size: 25px; margin: auto; padding: 15px;">Fiche Client</h3> 
    </div>
    <div class="d-flex justify-content-space-between" style="margin: 50px;">
        <!-- INFORMATION CLIENT -->
        <div class="d-flex flex-column" style="background-color: #C4C4C4; width: 45%; border-radius: 5px;">
            <h3 style="font-weight: bold;background-color: #262626; color: white; font-size: 23px; padding: 15px; text-align: center; border-radius: 5px 5px 0 0;">Client</h3> 
            <div class="d-flex">
                <table class="tabClient">
                    <tr>
                        <th scope="row" style="color:#02897A; width: 120px;">Nom</th>
                        <td style="color:#02897A;">{$user->getLastName()}</td>
                    </tr>
                    <tr style="background-color: #E3E3E3;">
                        <th scope="row">Prénom</th>
                        <td>{$user->getFirstName()}</td>
                    </tr>
                    <tr>
                        <th scope="row">Adresse</th>
                        <td>{$user->getRue()}</td>
                    </tr>
                    <tr>
                        <th scope="row">CP - Ville</th>
                        <td>{$user->getCp()} - {$user->getCity()}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="background-color: #E3E3E3;">Téléphone</th>
                        <td style="background-color: #E3E3E3;">{$user->getPhone()}</td>
                    </tr>
                    <tr>
                        <th scope="row">Mail</th>
                        <td>{$user->getEmail()}</td>
                    </tr>
                    <tr>
                        <th scope="row" style="color:#02897A; background-color: #E3E3E3;">Solde</th>
                        <td style="color:#02897A; background-color: #E3E3E3;">0.00</td>
                    </tr>
                </table>
            </div>
            <div class="d-flex justify-content-space-between" style="margin: 12px;">
                <input type='buttonNewPresta' class='button' value='Envoyer un SMS' style="text-align: center;" >
                <input type='buttonNewPresta' class='button' value='Envoyer un Email' style="text-align: center;">
                <form action="profile_animal.php" method="post">
                    <button class="buttonNewPresta" type="submit" name="animalId" value="{test}">Nouvelle Prestation</button>
                </form>
            </div>
        </div>
        <!-- TOUS LES ANIMAUX DU CLIENT -->
        <div class="d-flex flex-column" style="width: 45%; background-color: #E3E3E3; border-radius: 5px;">
            <h3 style="font-weight: bold;background-color: #262626; color: white; font-size: 23px; padding: 15px; text-align: center; border-radius: 5px 5px 0 0;">Animaux du Client</h3> 
            <table class="tabAnimaux">
                <thead>
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col">Espèce</th>
                        <th scope="col">Race</th>
                    </tr>
                </thead>
                <tbody>
                    $tabAnimaux
                </tbody>
            </table>
        </div>
    </div>
    <!-- FICHE ANIMAL DU CLIENT -->
    <div class="d-flex flex-column" style="background-color: #E3E3E3; margin: 20px 50px 20px 50px;">
        <h3 style="background-color: #262626; color: white; font-size: 25px; font-weight: bold; padding: 15px; text-align: center; width: 100%; border-radius: 5px 5px 0 0;">Fiche Animal du Client</h3> 
        <div class="d-flex flex-row">
            <div class="d-flex flex-column" style="width: 50%; border-right: 15px solid #C4C4C4;">
                <h3 style="background-color: #C4C4C4; color: white; font-size: 25px; font-weight: bold; padding: 15px; text-align: center; width: 100%;">Information général</h3> 
                <div class="d-flex flex-row">
                    <img src="img/rounded/rounded_Chien.png" alt="" height="330px" style="margin-left: 5px;">
                    <div class="d-flex flex-column pt-3 pl-4 justify-content-center" style=" font-size: 18px;">
                        <p style="margin: 0; font-weight: bold; color:#02897A;">Nom</p>
                        <p>{$animalSelect->getName()}</p><br>     
                        <p style="margin: 0; font-weight: bold; color:#02897A">Race</p>
                        <p>{$animalSelect->getNameRace()}</p><br>
                        <p style="margin: 0; font-weight: bold; color:#02897A">Genre</p>
                        <p>{$animalSelect->getGenderName()}</p>
                    </div>
                    <div class="d-flex flex-column pt-3 justify-content-center" style=" font-size: 18px; padding-left: 130px;">
                        <p style="margin: 0; font-weight: bold; color:#02897A;">Espèce</p>
                        <p>{$animalSelect->getSpecieName()}</p><br>
                        <p style="margin: 0; font-weight: bold; color:#02897A">Robe</p>
                        <p>{$animalSelect->getDress()}</p><br>
                        <p style="margin: 0; font-weight: bold; color:#02897A">Poids</p>
                        <p>{$animalSelect->getWeight()}</p>
                    </div>
                </div>
                <div style="font-size: 18px; margin: 30px 0 30px 25px;">
                    <p style="margin: 0; font-weight: bold; color:#02897A;">Age</p> 
                    <p>2 ans et 5 mois</p>
                    <p style="margin: 0; font-weight: bold; color:#02897A;">Date de Naissance</p> 
                    <p>10/01/2019</p>
                     <p style="margin: 0; font-weight: bold; color:#02897A;">Date de Décès</p> 
                    <p>Encore en vie le coquin !</p>
                    <p style="margin: 0; font-weight: bold; color:#02897A;">Tatouage</p> 
                    <p>304UJB</p>
                    <p style="margin: 0; font-weight: bold; color:#02897A;">N° Puce</p> 
                    <p>250 268 267 203 154</p>
                    <p style="margin: 0; font-weight: bold; color:#02897A;">Remarque</p> 
                    <p>Ne supporte pas le collier Seresto, se gratte</p>
                </div>
            </div>
            <div class="d-flex flex-column" style="width: 50%;">
                <div class="d-flex flex-column" style="height: 50%">
                    <h3 style="background-color: #C4C4C4; color: white; font-size: 25px; font-weight: bold; padding: 15px; text-align: center; width: 100%;">
                        <span style="padding-right: 180px;">{$webPage->getIcon('arrow-left', 28)}</span>Historique<span style="padding-left: 180px;">{$webPage->getIcon('arrow-right', 28)}</span>
                    </h3> 
                </div>
                <div class="d-flex flex-column" style="height: 50%">
                    <h3 style="background-color: #C4C4C4; color: white; font-size: 25px; font-weight: bold; padding: 15px; text-align: center; width: 100%;">Vaccins</h3> 
                    <table class="tabVaccins">
                        <tr>
                            <th scope="row">CH (Maladie de carré et hépatite de rubarth)</th>
                            <td>{$webPage->getIcon('valide', 28)}</td>
                            <td>27/12/2021</td>
                        </tr>
                        <tr>
                            <th scope="row">P (Parvovirose)</th>
                            <td>{$webPage->getIcon('valide', 28)}</td>
                            <td>27/12/2021</td>
                        </tr>
                        <tr>
                            <th scope="row">PI (Parainfluenza)</th>
                            <td>{$webPage->getIcon('valide', 28)}</td>
                            <td>27/12/2021</td>
                        </tr>
                        <tr>
                            <th scope="row">L4 (leptospirose)</th>
                            <td>{$webPage->getIcon('invalid', 28)}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">R (Rage)</th>
                            <td>{$webPage->getIcon('invalid', 28)}</td>
                            <td></td>
                        </tr>
                    </table>                
                </div>
            </div>
        </div>
    </div>
</div>
HTML;

$webPage->appendContent($html);
echo $webPage->toHTML();