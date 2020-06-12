<!--
Displays a field as a date picker (can be used with v-model)
-->

<template>
  <base-picker
    icon="mdi-calendar"
    :value="value"
    :format="format"
    :parse="parse"
    v-bind="$attrs"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-date-picker
        :value="picker.localValue"
        :locale="$i18n.locale"
        first-day-of-week="1"
        no-title
        scrollable
        @input="picker.on.input">
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close">Cancel</v-btn>
        <v-btn text color="primary" @click="picker.on.save">OK</v-btn>
      </v-date-picker>
    </template>

    <!-- passing the append slot through -->
    <template v-slot:append>
      <slot name="append" />
    </template>
  </base-picker>
</template>

<script>
import BasePicker from './BasePicker'
import moment from 'moment'

export default {
  name: 'DatePicker',
  components: { BasePicker },
  props: {
    value: { type: String, required: true }
  },
  methods: {
    format (val) {
      if (val) {
        return moment(val, moment.HTML5_FMT.DATE).locale(this.$i18n.locale).format('L')
      }
      return ''
    },
    parse (val) {
      if (val) {
        return moment(val, 'L', this.$i18n.locale).format(moment.HTML5_FMT.DATE)
      }
      return ''
    }
  }
}
</script>

<style scoped>
</style>