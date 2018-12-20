<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PollAnswersFixture
 *
 */
class PollAnswersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'poll_proposal_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'poll_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_poll_answers_users1_idx' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'fk_poll_answers_poll_proposals1_idx' => ['type' => 'index', 'columns' => ['poll_proposal_id'], 'length' => []],
            'fk_poll_answers_polls1_idx' => ['type' => 'index', 'columns' => ['poll_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_poll_answers_poll_proposals1' => ['type' => 'foreign', 'columns' => ['poll_proposal_id'], 'references' => ['poll_proposals', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_poll_answers_polls1' => ['type' => 'foreign', 'columns' => ['poll_id'], 'references' => ['polls', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_poll_answers_users1' => ['type' => 'foreign', 'columns' => ['user_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'created' => '2018-12-21 00:08:06',
                'modified' => '2018-12-21 00:08:06',
                'user_id' => 1,
                'poll_proposal_id' => 1,
                'poll_id' => 1
            ],
        ];
        parent::init();
    }
}
