<?php
/**
 * =====================================================
 * EUROCARE — BeneficiaireController
 * =====================================================
 */
defined('BASEPATH') or die('Accès direct interdit.');

class BeneficiaireController
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function dashboard(array $p = []): void
    {
        $user   = Auth::user();
        $dossier= $this->db->findOne('beneficiaires_profils',['user_id'=>$user['id']]);
        $aides  = $dossier ? $this->db->query("SELECT * FROM aides_octroyees WHERE beneficiaire_id=? ORDER BY cree_le DESC LIMIT 3",[$dossier['id']])->fetchAll() : [];
        $notifs = Helpers::getUnreadNotifications($user['id'],5);
        Helpers::view('beneficiary/dashboard',['pageTitle'=>'Mon espace','user'=>$user,'dossier'=>$dossier,'aides'=>$aides,'notifs'=>$notifs],'main');
    }

    public function dossier(array $p = []): void
    {
        $user   = Auth::user();
        $dossier= $this->db->findOne('beneficiaires_profils',['user_id'=>$user['id']]);
        $docs   = $dossier ? $this->db->findAll('documents',['user_id'=>$user['id']],'*','cree_le DESC') : [];
        Helpers::view('beneficiary/dossier',['pageTitle'=>'Mon dossier social','user'=>$user,'dossier'=>$dossier,'docs'=>$docs],'main');
    }

    public function saveDossier(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/beneficiaire/mon-dossier');
        $user   = Auth::user();
        $dossier= $this->db->findOne('beneficiaires_profils',['user_id'=>$user['id']]);

        $data = [
            'type_beneficiaire'    => Security::input('type_beneficiaire'),
            'situation_familiale'  => Security::input('situation_familiale'),
            'nombre_enfants'       => max(0,(int)Security::input('nombre_enfants')),
            'revenus_mensuels'     => filter_var(Security::input('revenus_mensuels'),FILTER_VALIDATE_FLOAT) ?: null,
            'situation_logement'   => Security::input('situation_logement'),
            'besoins_principaux'   => mb_substr(Security::input('besoins_principaux'),0,2000),
            'description_situation'=> mb_substr(Security::input('description_situation'),0,5000),
            'niveau_urgence'       => Security::input('niveau_urgence') ?: 'modere',
            'modifie_le'           => date('Y-m-d H:i:s'),
        ];

        if (!array_key_exists($data['type_beneficiaire'], TYPES_BENEFICIAIRE)) {
            Helpers::redirectWithFlash('/beneficiaire/mon-dossier','erreur','Type de bénéficiaire invalide.');
        }

        if ($dossier) {
            $this->db->update('beneficiaires_profils', $data, ['id'=>$dossier['id']]);
        } else {
            $data['user_id']        = $user['id'];
            $data['numero_dossier'] = Security::generateDossierNumber();
            $data['statut_dossier'] = 'en_attente';
            $data['cree_le']        = date('Y-m-d H:i:s');
            $this->db->insert('beneficiaires_profils', $data);
        }

        Logger::log(ACTION_BENEF_UPDATE,'beneficiaires_profils',$dossier['id']??null);
        Helpers::redirectWithFlash('/beneficiaire/mon-dossier','succes','Dossier sauvegardé avec succès.');
    }

    public function uploadDoc(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::jsonError('Token invalide.');
        $user    = Auth::user();
        $typeDoc = Security::input('type_document');
        if (!array_key_exists($typeDoc, TYPES_DOCUMENT)) Helpers::jsonError('Type de document invalide.');

        $result = Security::handleUpload('document','documents',ALLOWED_DOC_TYPES);
        if (!$result['success']) Helpers::jsonError($result['error']);

        $dossier = $this->db->findOne('beneficiaires_profils',['user_id'=>$user['id']],'id');
        $this->db->insert('documents',[
            'user_id'        => $user['id'],
            'beneficiaire_id'=> $dossier['id'] ?? null,
            'type_document'  => $typeDoc,
            'nom_original'   => mb_substr($result['original'],0,255),
            'nom_stockage'   => $result['filename'],
            'chemin'         => $result['path'],
            'taille'         => $result['size'],
            'mime_type'      => $result['mime'],
            'statut'         => 'en_attente',
            'cree_le'        => date('Y-m-d H:i:s'),
        ]);

        Logger::log(ACTION_UPLOAD_FILE,'documents',null,null,['type'=>$typeDoc]);
        Helpers::jsonSuccess('Document téléchargé avec succès.',['filename'=>$result['filename']]);
    }

    public function mesAides(array $p = []): void
    {
        $user   = Auth::user();
        $dossier= $this->db->findOne('beneficiaires_profils',['user_id'=>$user['id']],'id');
        $aides  = $dossier ? $this->db->query("SELECT ao.*,CONCAT(u.prenom,' ',u.nom) AS accordeur FROM aides_octroyees ao JOIN users u ON u.id=ao.accorde_par WHERE ao.beneficiaire_id=? ORDER BY ao.cree_le DESC",[$dossier['id']])->fetchAll() : [];
        Helpers::view('beneficiary/mes_aides',['pageTitle'=>'Mes aides reçues','aides'=>$aides],'main');
    }

    public function messages(array $p = []): void
    {
        $user = Auth::user();
        $msgs = $this->db->query("SELECT m.*,CONCAT(u.prenom,' ',u.nom) AS exp_nom FROM messages m LEFT JOIN users u ON u.id=m.expediteur_id WHERE m.destinataire_id=? AND m.archive_dest=0 ORDER BY m.cree_le DESC",[$user['id']])->fetchAll();
        Helpers::view('beneficiary/messages',['pageTitle'=>'Mes messages','messages'=>$msgs],'main');
    }

    public function envoyerMsg(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/beneficiaire/messages');
        $user  = Auth::user();
        $admin = $this->db->query("SELECT id FROM users WHERE role='admin' AND statut='actif' LIMIT 1")->fetchColumn();
        if (!$admin) Helpers::redirectWithFlash('/beneficiaire/messages','erreur','Impossible d\'envoyer le message.');

        $this->db->insert('messages',[
            'expediteur_id'   => $user['id'],
            'destinataire_id' => $admin,
            'sujet'           => mb_substr(Security::input('sujet'),0,255),
            'contenu'         => mb_substr(Security::input('contenu'),0,5000),
            'cree_le'         => date('Y-m-d H:i:s'),
        ]);
        Helpers::redirectWithFlash('/beneficiaire/messages','succes','Message envoyé avec succès.');
    }

    public function profil(array $p = []): void
    {
        Helpers::view('beneficiary/profil',['pageTitle'=>'Mon profil','user'=>Auth::user()],'main');
    }

    public function updateProfil(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/beneficiaire/profil');
        $user = Auth::user();
        $data = ['prenom'=>mb_substr(Security::input('prenom'),0,100),'nom'=>mb_substr(Security::input('nom'),0,100),'telephone'=>mb_substr(Security::input('telephone'),0,20),'pays'=>mb_substr(Security::input('pays'),0,100),'ville'=>mb_substr(Security::input('ville'),0,100),'adresse'=>mb_substr(Security::input('adresse'),0,500)];
        Database::getInstance()->update('users',$data,['id'=>$user['id']]);
        Auth::invalidateCache();
        Helpers::redirectWithFlash('/beneficiaire/profil','succes','Profil mis à jour.');
    }
}
