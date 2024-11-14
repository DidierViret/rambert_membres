<?php
/**
 * French translations for access module
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

return[
// Pages titles
'title_login'                       => 'Connexion',
'title_change_my_password'          => 'Modification de mon mot de passe',
'title_access_list'                 => 'Liste des droits d\'accès',
'title_access_update'               => 'Modifier les droits d\'accès',
'title_access_new'                  => 'Ajouter un droit d\'accès',
'title_access_delete'               => 'Supprimer un droit d\'accès',
'title_access_password_reset'       => 'Réinitialiser le mot de passe',
'title_administration'              => 'Administration',

// Forms fields
'field_email'                       => 'Adresse e-mail',
'field_password'                    => 'Mot de passe',
'field_old_password'                => 'Ancien mot de passe',
'field_new_password'                => 'Nouveau mot de passe',
'field_password_confirm'            => 'Confirmer le mot de passe',
'field_access_level'                => 'Niveau d\'accès',
'field_deleted_access_display'      => 'Afficher les droits d\'accès désactivés',

// Buttons
'btn_hard_delete_access'            => 'Supprimer ces droits d\'accès',
'btn_disable_access'                => 'Désactiver ces droits d\'accès',

// Error messages
'msg_error_access_denied_header'    => 'Accès interdit',
'msg_error_access_denied'           => 'Vous n\'avez pas l\'autorisation d\'accéder à cette fonction',
'msg_error_invalid_password'        => 'L\'e-mail et le mot de passe ne sont pas valides',
'msg_error_invalid_old_password'    => 'L\'ancien mot de passe n\'est pas valide',
'msg_error_access_already_inactive' => 'Les droits d\'accès sont déjà désactivés',
'msg_error_access_already_active'   => 'Les droits d\'accès sont déjà activés',
'msg_error_access_level_not_exist'  => 'Ce niveau d\'accès n\'existe pas',
'msg_error_invalid_password'        => 'L\'e-mail et le mot de passe ne sont pas valides',
'msg_error_invalid_old_password'    => 'L\'ancien mot de passe n\'est pas valide',
'msg_error_password_not_matches'    => 'Le mot de passe ne coïncide pas avec la confirmation du mot de passe',
'msg_error_default'                 => 'Une erreur est survenue',

// Error code messages
'code_error_401'                    => '401 - Non autorisé',
'code_error_403'                    => '403 - Accès refusé',

// Other texts
'access'                            => 'Droits d\'accès',
'access_delete'                     => 'Désactiver ou supprimer ces droits d\'accès',
'access_reactivate'                 => 'Réactiver ces droits d\'accès',
'access_disabled_info'              => 'Ces droits d\'accès sont désactivés. Vous pouvez les réactiver en cliquant sur le lien correspondant.',
'access_delete_explanation'         => 'La désactivation des droits d\'accès empêche la personne concernée de se connecter tout en conservant les informations qui lui sont liées.'
                                        .'Cela permet notamment de garder l\'historique de ses actions.<br><br>'
                                        .'En cas de suppression définitive, toutes les informations concernant ces droits d\'accès seront supprimées.',
'access_allready_disabled'          => 'Ces droits d\'accès sont déjà désactivés. Voulez-vous les supprimer définitivement ?',
'access_update_level_himself'       => 'Vous ne pouvez pas modifier votre propre niveau d\'accès. Cette opération doit être faite par un autre administrateur.',
'access_delete_himself'             => 'Vous ne pouvez pas désactiver ou supprimer vos propres droits d\'accès. Cette opération doit être faite par un autre administrateur.',
];