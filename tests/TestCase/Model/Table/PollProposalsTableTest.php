<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PollProposalsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PollProposalsTable Test Case
 */
class PollProposalsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PollProposalsTable
     */
    public $PollProposals;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.poll_proposals',
        'app.polls',
        'app.poll_answers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PollProposals') ? [] : ['className' => PollProposalsTable::class];
        $this->PollProposals = TableRegistry::getTableLocator()->get('PollProposals', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PollProposals);

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
