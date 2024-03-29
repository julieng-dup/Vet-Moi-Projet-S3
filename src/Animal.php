<?php declare(strict_types=1);

class Animal
{
    private string $animalId;
    private string $name;
    private string $birthDay;
    private ? string $deathDay;
    private ? string $comment;
    private ? string $dress;
    private ? string $weight;
    private ? string $tatoo;
    private ? string $chip;
    private string $userId;
    private int $threatId;
    private int $genderId;
    private int $raceId;

    /**
     * Return Animal object from an id.
     * @param int $id
     * @return static
     * @throws Exception
     */
    public static function createFromId(string $id):self
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Animal
        WHERE animalId=?
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Animal::class);
        $req->execute([$id]);
        $return=$req->fetch();
        if(!$return)
        {
            throw new InvalidArgumentException("Id not not in DataBase.");
        }
        return $return;
    }

    /**
     * Return all animal by birthDate (or columnName).
     * @param string|null $columnName
     * @return array
     * @throws Exception
     */
    public static function getAllPet(string $columnName=null):array
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Animal
        ORDER birthDay
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Animal::class);
        $req->execute();
        $return=$req->fetchAll();
        if(!$return)
            throw new Exception("No Pet in DataBase.");
        if($columnName!=null)
        {
            try {
                $columnOfSort = array_column($return, $columnName);
                array_multisort($columnOfSort, SORT_ASC, $return);
            }catch (Exception $e)
            {
                echo("$columnName : invalid column name ! ");
            }
        }
        return $return;
    }

    /**
     * Return all animal with the specified genderStatusId.
     * @param int $id
     * @return array
     * @throws Exception
     */
    public static function getPetByGenderStatus(int $id)
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Animal
        WHERE genderId=?
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Animal::class);
        $req->execute([$id]);
        $return=$req->fetchAll();
        if(!$return)
        {
            throw new InvalidArgumentException("No animal with this genderStatusId.");
        }
        return $return;
    }

    /**
     * Return all animal with the specific threatId.
     * @param int $id
     * @return array
     * @throws Exception
     */
    public static function getPetBythreat(int $id)
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Animal
        WHERE threatId=?
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Animal::class);
        $req->execute([$id]);
        $return=$req->fetchAll();
        if(!$return)
        {
            throw new InvalidArgumentException("No animal with this threatId.");
        }
        return $return;
    }

    /**
     * Return all animal of the specified raceId.
     * @param int $id
     * @return array
     * @throws Exception
     */
    public static function getPetByRace(int $id)
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Animal
        WHERE raceId=?
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Animal::class);
        $req->execute([$id]);
        $return=$req->fetchAll();
        if(!$return)
        {
            throw new InvalidArgumentException("No animal of this raceId.");
        }
        return $return;
    }

    /**
     * Return all animal of the specified speciesId
     * @param int $id
     * @return array
     * @throws Exception
     */
    public static function getPetBySpecies(int $id)
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Animal
        WHERE raceId IN(SELECT raceId
                        FROM Race
                        WHERE especeId=?)
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Animal::class);
        $req->execute([$id]);
        $return=$req->fetchAll();
        if(!$return)
        {
            throw new InvalidArgumentException("No animal of this species.");
        }
        return $return;
    }

    /**
     * Return all the meeting of the animal.
     * @return array
     * @throws Exception
     */
    public function getAllMeetings()
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Meeting
        WHERE animalId=?
        ORDER BY meetingDate
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Meeting::class);
        $req->execute([$this->animalId]);
        $return=$req->fetchAll();
        if(!$return)
        {
            throw new InvalidArgumentException("No meeting for this animal.");
        }
        return $return;
    }

    /**
     * Renvoie le dernier rendez-voys.
     * @return string
     * @throws Exception
     */
    public function getLastMeeting():string {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT * FROM Meeting
        WHERE animalId=?
        AND meetingDate < CURRENT_DATE
        ORDER BY meetingDate DESC
        LIMIT 1
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Meeting::class);
        $req->execute([$this->animalId]);
        $lastMeetings=$req->fetchAll();
        $date="";
        if(!$lastMeetings)
        {
            throw new InvalidArgumentException("No meeting for this animal.");
        } else {
            foreach($lastMeetings as $lastMeeting)
            {
                $date = ucwords(utf8_encode(strftime("%A %d %b %Y", strtotime($lastMeeting->getDateTime()))));
            }
            return $date;
        }
    }

    /**
     * Renvoie LES prochains meetings.
     * @return array
     * @throws Exception
     */
    public function getNextMeetings(): array
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Meeting
        WHERE animalId=?
        AND meetingDate > CURRENT_DATE 
        ORDER BY meetingDate
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Meeting::class);
        $req->execute([$this->animalId]);
        $return=$req->fetchAll();
        if(!$return)
        {
            throw new InvalidArgumentException("No meeting for this animal.");
        }
        return $return;
    }

    /**
     * Renvoi LE prochain rendez-vous.
     * @return string
     * @throws Exception
     */
    public function getNextMeeting():string {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT * FROM Meeting
        WHERE animalId=?
        AND meetingDate > CURRENT_DATE
        ORDER BY meetingDate
        LIMIT 1
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Meeting::class);
        $req->execute([$this->animalId]);
        $lastMeetings=$req->fetchAll();
        $date="";
        if(!$lastMeetings)
        {
            throw new InvalidArgumentException("No meeting for this animal.");
        } else {
            foreach($lastMeetings as $lastMeeting)
            {
                $date = ucwords(utf8_encode(strftime("%A %d %b %Y <br> %H:%M", strtotime($lastMeeting->getDateTime()))));
            }
            return $date;
        }
    }

    /**
     * Retourne tous les vaccins de l'animal
     * @return array
     * @throws Exception
     */
    public function getHisVaccine():array
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT * FROM Vaccinated
        WHERE animalId=?
        SQL);

        $req->setFetchMode(PDO::FETCH_CLASS, Vaccine::class);
        $req->execute([$this->animalId]);
        $hisVaccine=$req->fetchAll();
        if(!$hisVaccine)
        {
            throw new InvalidArgumentException("No vaccine for this animal.");
        }
        return $hisVaccine;
    }

    /**
     * @return int
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getRaceId(): int
    {
        return $this->raceId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string/null
     */
    public function getDeathDay()
    {
        return $this->deathDay;
    }

    /**
     * @return string
     */
    public function getBirthDay(): string
    {
        return $this->birthDay;
    }

    /**
     * @return int
     */
    public function getAnimalId(): string
    {
        return $this->animalId;
    }

    /**
     * @return string/null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return int
     */
    public function getGenderId(): int
    {
        return $this->genderId;
    }

    public function getGenderName():string
    {
        if($this->genderId == 1) {
            return 'Femelle';
        }elseif ($this->genderId == 2)
        {
            return 'Mâle';
        }
        return 'Autre';
    }

    /**
     * @return int
     */
    public function getThreatId(): int
    {
        return $this->threatId;
    }

    /**
     * @param int $genderId
     */
    public function setGenderId(int $genderId): void
    {
        $this->genderId = $genderId;
    }

    /**
     * @return string
     */
    public function getNameRace(): string {
        $race = Race::createFromId($this->getRaceID());
        return $race->getRaceName();
    }

    /**
     * @return string
     */
    public function getSpecieName(): string {
        $race = Race::createFromId($this->getRaceID());
        $specie = Species::createFromId($race->getSpeciesId());
        return $specie->getSpeciesName();
    }

    /**
     * @return string|null
     */
    public function getWeight(): ?string
    {
        return $this->weight;
    }

    /**
     * @param string|null $weight
     */
    public function setWeight(?string $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return string|null
     */
    public function getDress(): ?string
    {
        return $this->dress;
    }

    /**
     * @param string|null $dress
     */
    public function setDress(?string $dress): void
    {
        $this->dress = $dress;
    }

    /**
     * @return string|null
     */
    public function getTatoo(): ?string
    {
        return $this->tatoo;
    }

    /**
     * @param string|null $tatoo
     */
    public function setTatoo(?string $tatoo): void
    {
        $this->tatoo = $tatoo;
    }

    /**
     * @return string|null
     */
    public function getChip(): ?string
    {
        return $this->chip;
    }

    /**
     * @param string|null $chip
     */
    public function setChip(?string $chip): void
    {
        $this->chip = $chip;
    }

    public function hasVaccine(string $idVaccine): bool
    {
        $req=MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Vaccinated
        WHERE idVaccine=?
        AND idAnimal=?
        SQL);

        $req->execute([$idVaccine, $this->animalId]);
        if($req->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function getTableVaccin():string
    {
        $html ="";
        $race = Race::createFromId($this->getRaceID());
        $idSpecies = $race->getSpeciesId();

        $req = MyPDO::getInstance()->prepare(<<<SQL
        SELECT *
        FROM Vaccine
        WHERE idSpecies=?
        SQL
        );
        $req->setFetchMode(PDO::FETCH_CLASS, Vaccine::class);
        $req->execute([$idSpecies]);
        $vaccins = $req->fetchAll();
        if (!$vaccins) {
            return "<p style='align-self: center; font-size: 20px; padding-top: 50px;'>Aucun vaccin disponible pour cette espèce</p>";
        }
        foreach ($vaccins as $vaccin) {
                if($this->hasVaccine($vaccin->getIdVaccine())) {$isValid = 'valide';}
                else {$isValid = 'invalid';}
            $html.= <<< HTML
            <tr>
                <th scope="row">{$vaccin->getVaccineName()}</th>
                <td><img src="img/svg/icon-$isValid.svg" height="28" width="28" alt=""></td>
                <td>27/12/2021</td>
            </tr>
        HTML;
        }
        return $html;
    }
}