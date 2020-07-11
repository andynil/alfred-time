<?php

namespace Tests\Feature;

use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class TimerMenusTest extends TestCase
{
    /** @test */
    public function it_proposes_to_start_a_timer_if_there_is_at_least_one_timer_service_enabled()
    {
        Workflow::enableService('toggl');

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringContainsString('"arg":"choose_project"', $output);
    }

    /** @test */
    public function it_proposes_to_continue_a_timer_if_there_is_at_least_one_timer_service_enabled()
    {
        Workflow::enableService('toggl');

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringContainsString('"arg":"choose_timer"', $output);
    }

    /** @test */
    public function it_does_not_propose_to_start_a_timer_if_there_is_no_timer_services_enabled()
    {
        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringNotContainsString('"arg":"setup_timer"', $output);
    }

    /** @test */
    public function it_proposes_a_choice_of_projects_after_having_entered_the_timer_description()
    {
        Workflow::enableService('toggl');
        $this->togglApikey('wrong key');

        $output = $this->reachWorkflowChooseProjectMenu();

        $this->assertStringContainsString('"title":"No project"', $output);
    }

    /** @test */
    public function it_proposes_an_empty_project_amongst_the_choice_of_projects_if_the_service_allows_it()
    {
        Workflow::enableService('toggl');

        $this->assertStringContainsString('"title":"No project"', $this->reachWorkflowChooseProjectMenu());

        Workflow::destroy();

        Workflow::enableService('harvest');

        $this->assertStringNotContainsString('"title":"No project"', $this->reachWorkflowChooseProjectMenu());
    }

    /** @test */
    public function it_proposes_an_empty_tag_amongst_the_choice_of_tags_if_the_service_allows_it()
    {
        Workflow::enableService('toggl');

        $this->assertStringContainsString('"title":"No tag"', $this->reachWorkflowChooseTagMenu());

        Workflow::destroy();

        Workflow::enableService('harvest');

        $this->assertStringNotContainsString('"title":"No tag"', $this->reachWorkflowChooseTagMenu());
    }

    /** @test */
    public function it_proposes_a_choice_of_tags_after_having_chosen_a_project()
    {
        Workflow::enableService('toggl');
        $this->togglApikey('wrong key');

        $output = $this->reachWorkflowChooseTagMenu();

        $this->assertStringContainsString('"title":"No tag"', $output);
    }
}
