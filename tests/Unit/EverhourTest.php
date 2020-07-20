<?php

namespace Tests\Unit;

use Godbout\Alfred\Time\Services\Everhour;
use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class EverhourTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->everhour = new Everhour(getenv('EVERHOUR_APIKEY'));

        Workflow::enableService('everhour');

        $this->setEverhourTimerAttributes();

        sleep(4);
    }

    /** @test */
    public function it_returns_zero_project_if_the_service_cannot_authenticate()
    {
        $everhour = new Everhour('wrong apikey');

        $this->assertEmpty($everhour->projects());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_projects_if_the_service_can_authenticate()
    {
        $projects = $this->everhour->projects();

        $this->assertArrayHasKey(getenv('EVERHOUR_PROJECT_ID'), $projects);
        $this->assertSame(getenv('EVERHOUR_PROJECT_NAME'), $projects[getenv('EVERHOUR_PROJECT_ID')]);
    }

    /** @test */
    public function it_returns_zero_tag_if_the_service_cannot_authenticate()
    {
        $everhour = new Everhour('wrong apikey again');

        $this->assertEmpty($everhour->tags());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_tags_if_the_service_can_authenticate()
    {
        $tags = $this->everhour->tags();

        $this->assertArrayHasKey(getenv('EVERHOUR_TAG_ID'), $tags);
        $this->assertSame(getenv('EVERHOUR_TAG_NAME'), $tags[getenv('EVERHOUR_TAG_ID')]);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_return_the_list_of_past_timers()
    {
        $this->everhour->startTimer();
        $this->everhour->stopCurrentTimer();

        $latestTimer = $this->everhour->pastTimers()[0];

        $this->assertNotNull($latestTimer->id);
        $this->assertObjectHasAttribute('description', $latestTimer);
        $this->assertObjectHasAttribute('duration', $latestTimer);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        $this->assertNotFalse($this->everhour->startTimer());

        $this->everhour->stopCurrentTimer();
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_stop_a_timer()
    {
        $this->assertFalse($this->everhour->stopCurrentTimer());

        $this->everhour->startTimer();
        $this->assertTrue($this->everhour->stopCurrentTimer());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_get_the_running_timer()
    {
        $this->assertFalse($this->everhour->runningTimer());

        $timerId = $this->everhour->startTimer();
        $this->assertNotFalse($this->everhour->runningTimer());

        $this->everhour->stopCurrentTimer();
    }

    /**
     * @test
     * group timerServicesApiCalls
     */
    public function it_can_continue_a_timer()
    {
        $this->everhour->startTimer();
        $this->everhour->stopCurrentTimer();
        $previousTimer = $this->everhour->pastTimers()[0];

        $success = $this->everhour->continueTimer();
        $this->everhour->stopCurrentTimer();
        $latestTimer = $this->everhour->pastTimers()[0];

        $this->assertTrue($success);
        $this->assertStringContainsString($previousTimer->description, $latestTimer->description);
        $this->assertSame($previousTimer->project_id, $latestTimer->project_id);
        $this->assertSame($previousTimer->tags, $latestTimer->tags);
    }

    /** @test */
    public function a_Everhour_object_returns_toggl_as_a_string()
    {
        $this->assertSame('everhour', (string) $this->everhour);
    }

    /** @test */
    public function it_allows_empty_project_for_timer()
    {
        $this->assertTrue($this->everhour->allowsEmptyProject);
    }
}
