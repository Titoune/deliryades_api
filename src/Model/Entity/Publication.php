<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Publication Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property int $user_id
 * @property int $publication_type
 * @property string $title
 * @property string $content
 * @property string $website_url
 * @property string $website_title
 * @property string $website_picture
 * @property string $website_description
 * @property string $pdf
 * @property string $pdf_picture
 * @property string $video
 * @property string $video_url
 * @property string $video_iframe
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $published
 * @property bool $open_comment
 * @property int $like_count
 * @property int $comment_count
 * @property bool $cron_in_progress
 * @property bool $notified
 * @property bool $share
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\PublicationComment[] $publication_comments
 * @property \App\Model\Entity\PublicationLike[] $publication_likes
 */
class Publication extends Entity
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

    protected $_virtual = ['pdf_picture_sizes', 'video_picture_sizes', 'video_url', 'pdf_url', 'published_to_date_fr', 'published_to_time_fr'];



    protected function _getPdf_picture_sizes()
    {
        if ($this->pdf && $this->initial_publication_id) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'publications' . DS . 'shared' . DS . $this->initial_publication_id . DS . pathinfo($this->pdf, PATHINFO_FILENAME) . '.jpg');
        } elseif ($this->pdf) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'publications' . DS . $this->id . DS . pathinfo($this->pdf, PATHINFO_FILENAME) . '.jpg');
        } else {
            $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . 'introuvable.jpg');
        }

        return [
            'xs' => $pic_url . "&width=300&height=150",
            'sm' => $pic_url . "&width=600&height=300",
            'md' => $pic_url . "&width=800&height=400",
            'lg' => $pic_url . "&width=1200&height=600",
            'default' => $pic_url,
        ];
    }

    protected function _getVideo_picture_sizes()
    {
        if ($this->video && $this->initial_publication_id) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'publications' . DS . 'shared' . DS . $this->initial_publication_id . DS . pathinfo($this->video, PATHINFO_FILENAME) . '.jpg');
        } elseif ($this->video) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'publications' . DS . $this->id . DS . pathinfo($this->video, PATHINFO_FILENAME) . '.jpg');
        } else {
            $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . 'playbutton.jpg');
        }

        return [
            'xs' => $pic_url . "&width=300&height=150",
            'sm' => $pic_url . "&width=600&height=300",
            'md' => $pic_url . "&width=800&height=400",
            'lg' => $pic_url . "&width=1200&height=600",
            'default' => $pic_url,
        ];
    }


    protected function _getPdf_url()
    {
        if ($this->pdf && $this->initial_publication_id) {
            return WEBSITE_URL . 'medias' . DS . 'publications' . DS . 'shared' . DS . $this->initial_publication_id . DS . $this->pdf;
        } elseif ($this->pdf) {
            return WEBSITE_URL . 'medias' . DS . 'publications' . DS . $this->id . DS . $this->pdf;
        } else {
            return null;
        }
    }


    protected function _getVideo_url()
    {
        if ($this->video && $this->initial_publication_id) {
            return WEBSITE_URL . 'medias' . DS . 'publications' . DS . 'shared' . DS . $this->initial_publication_id . DS . $this->video;
        } elseif ($this->video) {
            return WEBSITE_URL . 'medias' . DS . 'publications' . DS . $this->id . DS . $this->video;
        } else {
            return null;
        }
    }


    protected function _getPublishedToDateFr()
    {
        if ($this->published) {
            return $this->published->i18nFormat('EEEE dd MMMM yyyy');
        } else {
            return null;
        }
    }

    protected function _getPublishedToTimeFr()
    {
        if ($this->published) {
            return $this->published->format('H:i');
        } else {
            return null;
        }
    }


    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setWebsite_url($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setWebsite_picture($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setWebsite_title($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setWebsite_description($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }


    protected function _setPdf($val)
    {
        return !empty($val) ? trim($val) : null;
    }


    protected function _setVideo($val)
    {
        return (!empty($val)) ? trim(urldecode($val)) : null;

    }

    protected function _setHosted_video($val)
    {
        return (!empty($val)) ? trim($val) : null;

    }
}
