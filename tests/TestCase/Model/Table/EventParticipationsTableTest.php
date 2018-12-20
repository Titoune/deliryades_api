<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EventParticipationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EventParticipationsTable Test Case
 */
class EventParticipationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\EventParticipationsTable
     */
    public $EventParticipations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.event_participations',
        'app.users',
        'app.events'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EventParticipations') ? [] : ['className' => EventParticipationsTable::class];
        $this->EventParticipations = TableRegistry::getTableLocator()->get('EventParticipations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EventParticipations);

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
