<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Lib\Types\DateUtc;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ScheduleEntryTest extends AbstractTestCase {
    public function testScheduleEntry() {
        $camp = new Camp();

        $start = new DateUtc();
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('PeriodDesc');
        $period->setStart($start);
        $period->setEnd($end);

        $activityCategory = new ActivityCategory();
        $activityCategory->setColor('#1fa2df');
        $activityCategory->setNumberingStyle('i');

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');
        $activity->setActivityCategory($activityCategory);

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->setPeriod($period);
        $scheduleEntry->setActivity($activity);
        $scheduleEntry->setPeriodOffset(600);
        $scheduleEntry->setLength(120);
        $scheduleEntry->setLeft(0);
        $scheduleEntry->setWidth(1);
        $period->addScheduleEntry($scheduleEntry);

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->setPeriod($period);
        $scheduleEntry->setActivity($activity);
        $scheduleEntry->setPeriodOffset(900);
        $scheduleEntry->setLength(120);
        $scheduleEntry->setLeft(0);
        $scheduleEntry->setWidth(1);
        $period->addScheduleEntry($scheduleEntry);

        $this->assertEquals($camp, $scheduleEntry->getCamp());
        $this->assertEquals($period, $scheduleEntry->getPeriod());
        $this->assertEquals($activity, $scheduleEntry->getActivity());
        $this->assertEquals($activityCategory, $scheduleEntry->getActivityCategory());
        $this->assertEquals(900, $scheduleEntry->getPeriodOffset());
        $this->assertEquals(120, $scheduleEntry->getLength());
        $this->assertEquals(0, $scheduleEntry->getLeft());
        $this->assertEquals(1, $scheduleEntry->getWidth());

        $this->assertEquals(1, $scheduleEntry->getDayNumber());
        $this->assertEquals('i', $scheduleEntry->getNumberingStyle());
        $this->assertEquals('1.ii', $scheduleEntry->getNumber());

        $this->assertEquals('#1fa2df', $scheduleEntry->getColor());
        $activityCategory->setColor('#FF00FF');
        $this->assertEquals('#FF00FF', $scheduleEntry->getColor());

        $duration = $scheduleEntry->getDuration();
        $this->assertEquals('120', $duration->format('%i'));
    }
}
