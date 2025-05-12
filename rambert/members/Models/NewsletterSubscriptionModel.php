<?php
/**
 * Model used to manage newsletter subscriptions
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;
use Members\Models\PersonModel;
use Members\Models\NewsletterModel;

class NewsletterSubscriptionModel extends Model {
    protected $table      = 'newsletter_subscription';
    protected $primaryKey = 'id';

    protected $allowedFields = ['fk_person', 'fk_newsletter'];

    protected $useSoftDeletes = false;

    // Callbacks
    protected $afterFind = ['appendNewsletter', 'appendPerson'];

    public function initialize()
    {
        $this->personModel = new PersonModel();
        $this->newsletterModel = new NewsletterModel();
    }

    /**
     * Callback method to append datas from the linked person table
     */
    protected function appendPerson(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_person'])) {
                $data['data']['person'] = $this->personModel->find($data['data']['fk_person']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$newsletterSubscription) {
                if (!empty($newsletterSubscription['fk_person'])) {
                    $newsletterSubscription['person'] = $this->personModel->find($newsletterSubscription['fk_person']);
                }
            }
        }
        return $data;
    }

    /**
     * Callback method to append datas from the linked newsletter table
     */
    protected function appendNewsletter(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if(!empty($data['data']['fk_newsletter'])) {
                $data['data']['newsletter'] = $this->newsletterModel->find($data['data']['fk_newsletter']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$newsletterSubscription) {
                if(!empty($newsletterSubscription['fk_newsletter'])) {
                    $newsletterSubscription['newsletter'] = $this->newsletterModel->find($newsletterSubscription['fk_newsletter']);
                }
            }
        }
        return $data;
    }
}
?>