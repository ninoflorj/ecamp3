<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Activity extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityContent", mappedBy="activity", cascade={"all"}, orphanRemoval=true)
     */
    protected $activityContents;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="activity", orphanRemoval=true)
     */
    protected $scheduleEntries;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="activity", orphanRemoval=true)
     */
    protected $activityResponsibles;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @var ActivityCategory
     * @ORM\ManyToOne(targetEntity="ActivityCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activityCategory;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $location = '';

    public function __construct() {
        parent::__construct();

        $this->activityContents = new ArrayCollection();
        $this->scheduleEntries = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
        $this->progress = 0;
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    /**
     * @internal Do not set the {@link Camp} directly on the Activity. Instead use {@see Camp::addActivity()}
     *
     * @param $camp
     */
    public function setCamp($camp) {
        $this->camp = $camp;
    }

    public function getActivityCategory(): ActivityCategory {
        return $this->activityCategory;
    }

    public function setActivityCategory(ActivityCategory $activityCategory): void {
        $this->activityCategory = $activityCategory;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getLocation(): string {
        return $this->location;
    }

    public function setLocation(string $location): void {
        $this->location = $location;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityContents() {
        return $this->activityContents;
    }

    public function addActivityContent(ActivityContent $activityContent) {
        $activityContent->setActivity($this);
        $this->activityContents->add($activityContent);
    }

    public function removeActivityContent(ActivityContent $activityContent) {
        $activityContent->setActivity(null);
        $this->activityContents->removeElement($activityContent);
    }

    /**
     * @return ArrayCollection
     */
    public function getScheduleEntries() {
        return $this->scheduleEntries;
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry) {
        $scheduleEntry->setActivity($this);
        $this->scheduleEntries->add($scheduleEntry);
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry) {
        $scheduleEntry->setActivity(null);
        $this->scheduleEntries->removeElement($scheduleEntry);
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityResponsibles() {
        return $this->activityResponsibles;
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible) {
        $activityResponsible->setActivity($this);
        $this->activityResponsibles->add($activityResponsible);
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible) {
        $activityResponsible->setActivity(null);
        $this->activityResponsibles->removeElement($activityResponsible);
    }
}
