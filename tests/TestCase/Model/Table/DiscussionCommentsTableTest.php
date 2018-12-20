<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DiscussionCommentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DiscussionCommentsTable Test Case
 */
class DiscussionCommentsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DiscussionCommentsTable
     */
    public $DiscussionComments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.discussion_comments',
        'app.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DiscussionComments') ? [] : ['className' => DiscussionCommentsTable::class];
        $this->DiscussionComments = TableRegistry::getTableLocator()->get('DiscussionComments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DiscussionComments);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
