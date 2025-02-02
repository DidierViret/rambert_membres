<?php
/**
 * Model used to manage newsletters
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;
use Members\Models\NewsletterSubscriptionModel;

class NewsletterModel extends Model {
    protected $table      = 'newsletter';
    protected $primaryKey = 'id';

    protected $allowedFields = ['title', 'date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField = 'date_delete';

    public function initialize()
    {
        $this->newsletterSubscriptionModel = new NewsletterSubscriptionModel();
    }
}
?>