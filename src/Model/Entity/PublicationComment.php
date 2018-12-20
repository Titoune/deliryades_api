<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;
use JCrowe\BadWordFilter\BadWordFilter;

/**
 * PublicationComment Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property int $publication_id
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $report_count
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Publication $publication
 */
class PublicationComment extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getContent($content)
    {
        if (!empty($content)) {
            $badwords = Options::getBadwordsArray();

            $myOptions = array("strictness" => "permissive", "also_check" => $badwords);
            $filter = new BadWordFilter($myOptions);
            $content = $filter->clean($content);

        }

        return $content;
    }

    protected function _getCreatedSql()
    {
        return !empty($this->created) ? $this->created->format('Y-m-d H:i:s') : null;
    }
}
