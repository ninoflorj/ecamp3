<template>
  <div>
    <e-text-field
      v-model="localActivity.title"
      :name="$tc('entity.activity.fields.title')"
      vee-rules="required" />

    <e-select v-model="localActivity.activityCategoryId" :label="$tc('entity.activity.fields.activityCategory')"
              :items="activityCategories.items"
              item-value="id"
              item-text="name"
              vee-rules="required">
      <template #item="{item, on, attrs}">
        <v-list-item :key="item.id" v-bind="attrs" v-on="on">
          <v-list-item-avatar>
            <v-chip :color="item.color">{{ item.short }}</v-chip>
          </v-list-item-avatar>
          <v-list-item-content>
            {{ item.name }}
          </v-list-item-content>
        </v-list-item>
      </template>
      <template #selection="{item}">
        <div class="v-select__selection">
          <span class="black--text">
            {{ item.name }}
          </span>
          <v-chip x-small :color="item.color">{{ item.short }}</v-chip>
        </div>
      </template>
    </e-select>

    <e-text-field
      v-model="localActivity.location"
      :name="$tc('entity.activity.fields.location')" />

    <create-schedule-entries v-if="activity.scheduleEntries" :schedule-entries="activity.scheduleEntries" />
  </div>
</template>

<script>
import CreateScheduleEntries from '@/components/activity/CreateScheduleEntries'

export default {
  name: 'DialogActivityForm',
  components: { CreateScheduleEntries },
  props: {
    activity: {
      type: Object,
      required: true
    },
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      localActivity: this.activity
    }
  },
  computed: {
    activityCategories () {
      return this.camp().activityCategories()
    }
  }
}
</script>
