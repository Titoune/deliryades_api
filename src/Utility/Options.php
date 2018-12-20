<?php

namespace App\Utility;


use Cake\Utility\Text;

class Options
{
    public static function getNegociationTypes($index = null)
    {
        $array = [
            1 => 'Hors marchés à procédure adaptée',
            2 => 'Marchés à procédure adaptée'
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getNegociationStatus($index = null)
    {
        $array = [
            1 => __("Demande reçue"),
            2 => __("En cours de traitement"),
            3 => __("Traitement terminé"),
            4 => __("Traitement impossible"),
            5 => __("Demande annulée")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getProcurationAuthorizations($index = null)
    {
        $array = [
            1 => __("Accès interdit"),
            2 => __("Lecture seule"),
            3 => __("Contrôle total"),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getProcurationAuthorizationsReadControl($index = null)
    {
        $array = [
            2 => __("Lecture seule"),
            3 => __("Contrôle total"),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getProcurationAuthorizationsForbidControl($index = null)
    {
        $array = [
            1 => __("Accès interdit"),
            3 => __("Contrôle total"),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }


    public static function getProcurationAuthorization($index = null)
    {
        $array = [
            1 => "<i class='fa fa-times'></i> Accès refusé",
            2 => "<i class='fa fa-eye'></i> Accès en lecture seule",
            3 => "<i class='fa fa-check'></i> Contrôle total"
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);
    }

    public static function getNotificationFrequencyOptions($index = null)
    {
        $array = [
            1 => __("en temps réel"),
            2 => __("une fois par jour"),
            3 => __("une fois par semaine"),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getNotificationOptions($index = null)
    {
        $array = [
            1 => __("jamais"),
            2 => __("sur le site"),
            3 => __("sur le site et par mail"),
            4 => __("sur le site / par mail / par sms")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }


    public static function getSubscriptionPrice($pop)
    {
        if ($pop <= 500)
            return ['subscription' => 125, 'initialcost' => 0];
        if ($pop > 500 && $pop <= 700)
            return ['subscription' => 140, 'initialcost' => 0];
        if ($pop > 700 && $pop <= 1200)
            return ['subscription' => 180, 'initialcost' => 500];
        if ($pop > 1200 && $pop <= 2000)
            return ['subscription' => 230, 'initialcost' => 700];
        if ($pop > 2000 && $pop <= 4000)
            return ['subscription' => 285, 'initialcost' => 900];
        if ($pop > 4000 && $pop <= 6000)
            return ['subscription' => 330, 'initialcost' => 1100];
        if ($pop > 6000 && $pop <= 8000)
            return ['subscription' => 370, 'initialcost' => 1250];
        if ($pop > 8000 && $pop <= 10000)
            return ['subscription' => 440, 'initialcost' => 1350];
        if ($pop > 10000 && $pop <= 20000)
            return ['subscription' => 540, 'initialcost' => 1450];
        if ($pop > 20000 && $pop <= 30000)
            return ['subscription' => 630, 'initialcost' => 1540];
        if ($pop > 30000 && $pop <= 40000)
            return ['subscription' => 730, 'initialcost' => 1620];
        if ($pop > 40000 && $pop <= 50000)
            return ['subscription' => 820, 'initialcost' => 1710];
        if ($pop > 50000 && $pop <= 100000)
            return ['subscription' => 910, 'initialcost' => 1960];
        if ($pop > 100000)
            return ['subscription' => 0, 'initialcost' => 0];

    }

    public static function getVoteOptionArray($index = null)
    {
        $array = [
            1 => __("Pour"),
            2 => __("Contre")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getSuggestionStatusOptions($index = null)
    {
        $array = [
            1 => __("En attente de validation"),
            2 => __("Validée"),
            3 => __("Clôturée"),
            4 => __("Suspendue par le maire"),
            5 => __("Refusée par le maire")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getSuggestionCategoryOptions($index = null)
    {
        $array = [
            1 => __("Agriculture"),
            2 => __("Culture"),
            3 => __("Ecologie & Energie"),
            4 => __("Economie & Industrie"),
            5 => __("Education & Recherche"),
            6 => __("Emploi"),
            7 => __("Famille et Jeunesse"),
            8 => __("Finances"),
            9 => __("Institutions & politiques"),
            10 => __("Justice"),
            11 => __("Logement"),
            12 => __("Numérique"),
            13 => __("Sécurité & défense"),
            14 => __("Social & santé"),
            15 => __("Sports & loisirs"),
            16 => __("Transport"),

        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getSignalingStatusOptions($index = null)
    {
        $array = [
            1 => __("En attente de prise en charge"),
            2 => __("Pris en charge"),
            3 => __("Ne peut pas être pris en charge"),
            4 => __("Déja signalé")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getEngagementPercentageArray($index = null)
    {
        $array = [
            50 => __("50%"),
            55 => __("55%"),
            60 => __("60%"),
            65 => __("65%"),
            70 => __("70%"),
            75 => __("75%"),
            80 => __("80%"),
            85 => __("85%"),
            90 => __("90%"),
            95 => __("95%"),
            100 => __("100%")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getSexOptionArray($index = null)
    {
        $array = [
            'i' => __('Indéfini'),
            'm' => __('Homme'),
            'f' => __('Femme'),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getSinceOptionArray()
    {
        $options = [];
        for ($i = date('Y'); $i >= date('Y') - 100; $i--) {
            $options[$i] = $i;
        }

        return $options;
    }

    public static function getDurationOptionArray($index = null)
    {
        $array = [86400 => __("1 journée"), 604800 => __("1 semaine"), 2592000 => __("1 mois"), 7776000 => __("3 mois"), 15552000 => __("6 mois"), 31536000 => __("1 an"), 126144000 => __("4 ans")];
        return ($index != null ? ($array[$index] ?? null) : $array);


    }

    public static function getValidationTypeArray($index = null)
    {
        $array = [1 => __("Automatique"), 0 => __("Manuelle")];
        return ($index != null ? ($array[$index] ?? null) : $array);


    }

    public static function getExchangeTypeArray($index = null)
    {
        $array = [1 => __("Téléphonique"), 2 => __("Email"), 3 => __("Courrier"), 4 => __("Physique"), 5 => 'Autres'];
        return ($index != null ? ($array[$index] ?? null) : $array);


    }

    public static function getSmscampaignStatusOptions($index = null)
    {
        $array = [
            1 => __("En cours de préparation"),
            2 => __("Création des sms"),
            3 => __("Sms en cours d'envoi"),
            4 => __("Terminée")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getAdtypes($index = null)
    {
        $array = [
            1 => __("Contenu sponsorisé"),
            2 => __("Sponsor Mécène")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getExpirationtypes($index = null)
    {
        $array = [
            1 => __("Nb de clics"),
            2 => __("Nb de Vues"),
            3 => __("Date d'expiration")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getVatOptions($index = null)
    {
        $array = [
            0 => '0.00%',
            20 => '20%'
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getTransfertypes($index = null)
    {
        $array = [
            1 => __("Virement bancaire"),
            2 => __("Chèque"),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);
    }

    public static function getClassifiedAdTypes($index = null)
    {
        $array = [
            1 => __("Je propose"),
            2 => __("Je recherche")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);
    }


    public static function getStatisticTypes($index = null)
    {
        $array = [
            1 => __("Affichage actualités"),
            2 => __("Affichage actualité"),
            3 => __("Affichage sondages"),
            4 => __("Affichage sondage"),
            5 => __("Affichage suggestions"),
            6 => __("Affichage suggestion"),
            7 => __("Affichage signalements"),
            8 => __("Affichage signalement"),
            9 => __("Affichage groupes de discussion"),
            10 => __("Affichage groupe de discussion"),
            11 => __("Affichage Evènements"),
            12 => __("Affichage Evènement"),
            13 => __("Affichage Le maire"),
            14 => __("Affichage la commune"),
            15 => __("Affichage mes notifications"),
            16 => __("Affichage messagerie"),
            17 => __("Affichage mon compte"),
            18 => __("Affichage petites annonces"),
            19 => __("Affichage petite annonce"),
            20 => __("Affichage partenaires"),
            21 => __("Procédures administratives"),
            22 => __("Elus de la commune"),
            23 => __("Service de la commune"),

        ];

        return ($index != null ? ($array[$index] ?? null) : $array);
    }

    public static function getPublicationTypes($index = null)
    {

        $array = [
            1 => __("Classique"),
            2 => __("Liens web"),
            3 => __("Photos"),
            4 => __("Videos"),
            5 => __("PDF"),

        ];

        return ($index != null ? ($array[$index] ?? null) : $array);
    }

    public static function getSmscampaignTypesOptions($index = null)
    {
        $array = [
            1 => __("Tous les citoyens"),
            2 => __("Groupes seulement"),
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);
    }


    public static function getAlertTypes($index = null)
    {
        $array = [
            1 => __("Alerte météo"),
            2 => __("Alerte incendie"),
            3 => __("Alerte innondation"),
            4 => __("Alerte travaux"),
            5 => __("Alerte accident"),
            6 => __("Alerte coupure d'électricité"),
            7 => __("Alerte coupure de gaz")
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }

    public static function getCityNegociationTypes($index = null)
    {
        $array = [
            1 => 'Hors marchés à procédure adaptée',
            2 => 'Marchés à procédure adaptée'
        ];

        return ($index != null ? ($array[$index] ?? null) : $array);

    }


    public static function getBadwordsArray()
    {
        return [
            "chatte",
            "enculer",
            "connards",
            "fesse",
            "enculés",
            "abruti",
            "ahuri",
            "aigrefin",
            "anachorète",
            "analphabète",
            "andouille",
            "anus",
            "arsouille",
            "attardé",
            "babache",
            "bachibouzouk",
            "balai de chiottes",
            "baltringue",
            "bandit",
            "barjot",
            "batârd",
            "bigleux",
            "blaireau",
            "boloss",
            "bordel à cul",
            "pd",
            "bouffon",
            "bouricot",
            "bourique",
            "bourrin",
            "boursemolle",
            "boursouflure",
            "bouseux",
            "boutonneux",
            "branleur",
            "branlotin",
            "branque",
            "branquignole",
            "brigand",
            "brêle",
            "burne",
            "bécasse",
            "bégueule",
            "bélitre",
            "béotien",
            "bougnoule",
            "cageot",
            "cagole",
            "calice",
            "canaille",
            "canaillou",
            "cancrelat",
            "caprinophile",
            "caribou",
            "casse-pieds",
            "cassos",
            "catin",
            "cervelle d'huitre",
            "chacal",
            "chacal puant",
            "chafouin",
            "chancreux",
            "chancre puant",
            "chaoui",
            "charogne",
            "chenapan",
            "chiassard",
            "caca",
            "chieur",
            "chier",
            "chiure de pigeon",
            "cinglé",
            "clampin",
            "cloaque",
            "cloche",
            "clodo",
            "cloporte",
            "clown",
            "cocu",
            "conard",
            "conchieur",
            "connard",
            "connasse",
            "conne",
            "coprolithe",
            "coprophage",
            "cornard",
            "cornegidouille",
            "corniaud",
            "couard",
            "couille",
            "couillon",
            "crassard",
            "crasspouillard",
            "crevard",
            "crevure",
            "crotte de moineau",
            "cryptorchide",
            "crâne d'obus",
            "crétin",
            "cuistre",
            "dégueulasse",
            "ducon",
            "dugenou",
            "dugland",
            "dypterosodomite",
            "débile",
            "décamerde",
            "décérébré",
            "dégueulis",
            "dégénéré",
            "dépravé",
            "ecervelé",
            "ectoplasme",
            "emmerdeur",
            "empaffé",
            "enculeur",
            "enculé",
            "enflure",
            "enfoiré",
            "eunuque",
            "face de pet",
            "face de rat",
            "faquin",
            "fesses",
            "feuj",
            "fiente",
            "filou",
            "fils de pute",
            "fion",
            "fiote",
            "foldingue",
            "fourbe",
            "foutriquet",
            "frapadingue",
            "freluquet",
            "fricoteur",
            "fripouille",
            "frippon",
            "fumiste",
            "furoncle",
            "félon",
            "ganache",
            "gangrène",
            "garage a bite",
            "gibier de potence",
            "glandeur",
            "glandus",
            "globicéphale",
            "gnome",
            "godiche",
            "gogol",
            "goinfre",
            "gommeux",
            "gougnafier",
            "goujat",
            "goulu",
            "gourgandine",
            "gras du bide",
            "graveleux",
            "gredin",
            "grenouille",
            "gringalet",
            "grognasse",
            "gros lard",
            "grosse merde puante",
            "gueulard",
            "gueule de fion",
            "gueule de raie",
            "gueux",
            "gugus",
            "guignol",
            "hérétique",
            "histrion",
            "homoncule",
            "hurluberlu",
            "hérétique",
            "iconoclaste",
            "idiot",
            "ignare",
            "imbibé",
            "imbécile",
            "impuissant",
            "infâme raie de cul",
            "ivrogne",
            "jean-foutre",
            "jobard",
            "jobastre",
            "judas",
            "kroumir",
            "kéké",
            "laideron",
            "lavedu",
            "lépreux",
            "loboto",
            "loutre analphabète",
            "malandrin",
            "malotru",
            "malpropre",
            "manant",
            "mange merde",
            "maquereau",
            "maquerelle",
            "maraud",
            "margoulin",
            "merdaillon",
            "merdasse",
            "merde",
            "merde molle",
            "merdophile",
            "merlan frit",
            "microcéphale",
            "minus",
            "miteux",
            "moins que rien",
            "molasson",
            "mongol",
            "mononeuronal",
            "mont de brin",
            "morbleu",
            "morfale",
            "morille",
            "morpion",
            "morue",
            "morveux",
            "motherfucker",
            "mou du bulbe",
            "mou du genou",
            "mou du gland",
            "moudlabite",
            "moule à gauffre",
            "mouton de panurge",
            "mérule",
            "nabot",
            "nanar",
            "naze",
            "nazillon",
            "necropédophile",
            "neuneu",
            "nez de boeuf",
            "niais, niaiseux",
            "nigaud",
            "niquer",
            "nique",
            "niguedouille",
            "noob",
            "nounouille",
            "nécrophile",
            "obsédé",
            "olibrius",
            "ordure purulente",
            "outre à pisse",
            "outrecuidant",
            "pachyderme",
            "paltoquet",
            "panaris",
            "parasite",
            "parbleu",
            "patate",
            "paumé",
            "paysan",
            "petite merde",
            "petzouille",
            "phlegmon",
            "pignolo",
            "pignouf",
            "pimbêche",
            "pinailleur",
            "pine d'ours",
            "pine d'huitre",
            "pipistrelle puante",
            "piqueniquedouille",
            "pisse froid",
            "pisse-vinaigre",
            "pisseuse",
            "pissure",
            "piètre",
            "planqué",
            "pleutre",
            "plouc",
            "poivrot",
            "polisson",
            "poltron",
            "pompe a merde",
            "pouacreux",
            "pouffe",
            "pouffiasse",
            "poufieux",
            "pouilleux",
            "pourceau",
            "pourriture",
            "pousse mégot",
            "putassière",
            "pute",
            "pute borgne",
            "putréfaction",
            "pygocéphale",
            "pécore",
            "pédale",
            "péquenot",
            "pétasse",
            "pétochard",
            "quadrizomique",
            "queutard",
            "raclure de bidet",
            "raclure de chiotte",
            "radasse",
            "radin",
            "sac à brin",
            "sac à foutre",
            "sac à gnole",
            "sac à merde",
            "sac à viande",
            "sac à vin",
            "sacrebleu",
            "sacrement",
            "sacripan",
            "sagouin",
            "salaud",
            "saligaud",
            "salopard",
            "salope",
            "saloperie",
            "salopiaud",
            "saperlipopette",
            "saperlotte",
            "scaphandrier d'eau de vaiselle",
            "scatophile",
            "scelerat",
            "schnock",
            "schpountz",
            "serpillière à foutre",
            "sinistrose ambulante",
            "sinoque",
            "sodomite",
            "sodomie",
            "sombre conne",
            "sombre crétin",
            "souillon",
            "sous merde",
            "tabarnak",
            "tabernacle",
            "tâcheron",
            "tafiole",
            "tanche",
            "tartignole",
            "taré",
            "tas de saindoux",
            "tasse à foutre",
            "tire couilles",
            "tocard",
            "tonnerre de brest",
            "toqué",
            "trainé",
            "traîne savate",
            "tricard",
            "triple buse",
            "tromblon",
            "tronche de cake",
            "trou de balle",
            "trou du cul",
            "troubignole",
            "truand",
            "trumeaux",
            "tête d'ampoule",
            "tête de bite",
            "bite",
            "chibre",
            "tête de con",
            "tête de noeud",
            "tête à claques",
            "va nu pieds",
            "va te faire",
            "vaurien",
            "ventrebleu",
            "vermine",
            "veule",
            "vicelard",
            "vieille baderne",
            "vieille poule",
            "vieille taupe",
            "vieux chnoque",
            "vieux con",
            "vieux fossile",
            "vieux tableau",
            "vieux tromblon",
            "vilain",
            "vioque",
            "vipère lubrique",
            "voleur",
            "vorace",
            "voyou",
            "vérole",
            "zigomar",
            "zigoto",
            "zonard",
            "zouave",
            "zoulou"
        ];
    }

}
