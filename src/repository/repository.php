<?php

require_once dirname(__FILE__) . "/../model/resources.php";
require_once dirname(__FILE__) . "/../model/testimonies.php";
require_once dirname(__FILE__) . "/../model/user.php";
require_once dirname(__FILE__) . "/../database/database.php";

class FaithGuardRepository
{
    // GET ALL
    public static function getAllResources()
    {
        $arr = Database::getRows("SELECT r.id, r.title, r.description, r.category, r.type, r.difficulty, r.tags
        FROM resources r
        JOIN users u ON r.user_id = u.id", null, "Resources");
        return $arr;
    }

    public static function getAllQuizzes()
    {
        $arr = Database::getRows("SELECT q.id, q.user_id, q.duration, q.accountability, q.resources_of_interest, q.spiritual_connection, q.primary_goal, q.created_at, u.username FROM quizzes q
        JOIN users u ON q.user_id = u.id", null, "Quiz");
        return $arr;
    }

    public static function getAllTestimonies()
    {
        $arr = Database::getRows("SELECT t.id, t.quote_text, t.cite_name, t.approved
        FROM testimonies t
        JOIN users u ON t.user_id = u.id", null, "Testimony");
        return $arr;
    }

    // GET ALL BY ID

    public static function getResourcesById($parId)
    {
        $item = Database::getSingleRow("SELECT r.id, r.title, r.description, r.category, r.type, r.difficulty, r.tags
        FROM resources r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = ?", [$parId], "Resources");
        return $item;
    }

    public static function getQuizzesById($parId)
    {
        $item = Database::getSingleRow("SELECT s.id, s.titel,s.beschrijving, s.afbeelding, s.prijs, s.nieuweCollectie, k.kleur_id, k.naam, m.maat_id, m.naam, m.afkorting, t.type_id, t.naam 
        FROM sportkleding s
        JOIN maten m ON s.maatId = m.maat_id
        JOIN types t ON s.typeId = t.type_id
        JOIN kleuren k ON s.kleurId = k.kleur_id
        WHERE s.id = ?", [$parId], "Kleding");
        return $item;
    }

    public static function getAllTestemoniesById($parId)
    {
        $item = Database::getSingleRow("SELECT s.id, s.titel,s.beschrijving, s.afbeelding, s.prijs, s.nieuweCollectie, k.kleur_id, k.naam, m.maat_id, m.naam, m.afkorting, t.type_id, t.naam 
        FROM sportkleding s
        JOIN maten m ON s.maatId = m.maat_id
        JOIN types t ON s.typeId = t.type_id
        JOIN kleuren k ON s.kleurId = k.kleur_id
        WHERE s.id = ?", [$parId], "Kleding");
        return $item;
    }

    public static function getAllCategories()
    {
        $arr = Database::getRows("SELECT * FROM types", null, "Type");
        return $arr;
    }

    public static function getProductsByTypeID($parTypeId)
    {
        $item = Database::getRows("SELECT * FROM sportkleding s WHERE s.typeId=?", [$parTypeId], "Type");
        return $item;
    }

    public static function getAllMaten()
    {
        $arr = Database::getRows("SELECT * FROM maten", null, "Maat");
        return $arr;
    }

    public static function getProductsByMaatID($parMaatId)
    {
        $item = Database::getRows("SELECT * FROM maten m WHERE m.maat_id=?", [$parMaatId], "Maat");
        return $item;
    }

    public static function getAllKleuren()
    {
        $arr = Database::getRows("SELECT * FROM kleuren", null, "Kleur");
        return $arr;
    }

    public static function getProductsByKleurID($parKleurId)
    {
        $item = Database::getRows("SELECT * FROM kleur k WHERE k.kleur_id=?", [$parKleurId], "Kleur");
        return $item;
    }

    // CRUD (CREATE, READ, UPDATE, DELETE)

    public static function updateProduct($parId, $parTitel, $parBeschrijving, $parAfbeelding, $parPrijs, $parNieuweCollectie, $parTypeId)
    {
        $item = Database::execute("UPDATE sportkleding SET titel = ?, beschrijving = ?, afbeelding = ?, prijs = ?, nieuweCollectie = ? WHERE typeId=? AND id =?", [$parTitel, $parBeschrijving, $parAfbeelding, $parPrijs, $parNieuweCollectie, $parTypeId, $parId], "Kleding");
        return $item;
    }

    public static function createProduct($parId, $parTitel, $parBeschrijving, $parAfbeelding, $parPrijs, $parNieuweCollectie, $parTypeId)
    {
        $item = Database::execute("INSERT INTO sportkleding (id, titel, beschrijving, afbeelding, prijs, nieuweCollectie, typeId) VALUES (?,?,?,?,?,?,?,?)", [$parId, $parTitel, $parBeschrijving, $parAfbeelding, $parPrijs, $parNieuweCollectie, $parTypeId], "Kleding");
        return $item;
    }

    public static function deleteProduct($id)
    {
        $item = Database::execute("DELETE FROM lampen WHERE id=?", [$id], "Lamp");
        return $item;
    }

    public static function getUserByLogin($login)
    {
        $item = Database::getSingleRow("SELECT * FROM users WHERE login=?", [$login], "User");
        return $item;
    }

    public static function createUser($login, $paswoord)
    {
        $res = Database::execute("INSERT INTO users(login,paswoord) VALUES (?,?)", [$login, $paswoord]);
        return $res;
    }

    public static function deleteUser($login, $paswoord)
    {
        $res = Database::execute("DELETE FROM users(login,paswoord) VALUES (?,?)", [$login, $paswoord]);
        return $res;
    }
}
