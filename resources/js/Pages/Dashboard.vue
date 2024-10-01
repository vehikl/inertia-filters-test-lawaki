<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {computed} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useSearchItem} from "@/Composables/useSearchItem";

type Filter<TKey extends string = string> = {
  options: Record<TKey, string[]>
  selected: Record<TKey, string | null>
}

type Pagination = {
  page: number
  limit: number
  pages: number
  total: number
}

type Defaults = {
  limit: number
  page: number
}

type TableRecord = {
  id: number,
  name: string,
  brand: string,
  company: string
}

type Props = {
  filter: Filter<'brand' | 'company' | 'department'>,
  records: TableRecord[]
  pagination: Pagination
  defaults: Defaults
  employee: { id: number, name: string } | null
}

const props = defineProps<Props>()

const pages = computed(() => Array.from({length: props.pagination.pages}, (_, i) => i + 1))

const { search, suggestions } = useSearchItem(() => props.employee ?  ({ key: props.employee.id, label: props.employee.name}) : null)

const query = computed(() => ({
  ...props.filter.selected,
  page: props.pagination.page,
  limit: props.pagination.limit,
  employee_id: props.employee?.id,
}))

function sanitizePayload(data: Record<string, unknown>): Record<string, unknown> {
  return Object.fromEntries(Object.entries(data).filter(([key, value]) => {
    if (Object.hasOwn(props.defaults, key)) {
      return props.defaults[key as keyof Defaults] !== value
    }

    return value != null
  }))
}

function reload(data: Record<string, unknown>): void {
  router.get(route('dashboard'), sanitizePayload(data) as Record<string, string>, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

function updateFilter(key: string, value: unknown): void {
  reload({
    ...query.value,
    [key]: value,
    page: 1,
    employee_id: null
  })
}

function resetFilter(data: Record<string, unknown> = {}): void {
  reload({ limit: props.pagination.limit, ...data })
}

function changePage(page: number): void {
  reload({...query.value, page})
}

type HandlingOptions = {
  as: 'string' | 'number',
  reset: boolean,
}

function handleFilterChange(key: string, e: Event, {as = 'string', reset = false}: Partial<HandlingOptions> = {}): void {
  const target = e.target as HTMLInputElement & HTMLSelectElement & HTMLTextAreaElement
  const value = as === 'string' ? target.value : Number(target.value)

  if (reset) {
    resetFilter({[key]: value})
    return
  }

  updateFilter(key, value)
}

</script>

<template>
  <Head title="Dashboard"/>

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="text-xl font-semibold leading-tight text-gray-800"
      >
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div
          class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
        >
          <div class="p-6 text-gray-900 flex flex-col gap-4 items-start">
            <label class="block">
              <span class="block">Brand</span>
              <select
                :value="filter.selected.brand"
                @change="(e) => handleFilterChange('brand', e)"
              >
                <option v-for="option in filter.options.brand">{{ option }}</option>
              </select>
            </label>

            <label class="block">
              <span class="block">Company</span>
              <select
                :value="filter.selected.company"
                @change="(e) => handleFilterChange('company', e)"
              >
                <option v-for="option in filter.options.company">{{ option }}</option>
              </select>
            </label>

            <label class="block">
              <span class="block">Employee</span>
              <input v-model="search">
              <select v-if="suggestions.length"
                :value="props.employee?.id"
                @change="(e) => handleFilterChange('employee_id', e, {as: 'number', reset: true})"
              >
                <option v-for="{key, label} in suggestions" :value="key">{{ label }}</option>
              </select>
            </label>

            <SecondaryButton @click="resetFilter">Reset</SecondaryButton>
          </div>

          <table class="m-6">
            <thead>
            <tr>
              <th class="border border-black p-2">ID</th>
              <th class="border border-black p-2">Name</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="record in records">
              <td class="border border-black p-2">{{ record.id }}</td>
              <td class="border border-black p-2">{{ record.name }}</td>
            </tr>
            </tbody>
          </table>

          <div class="inline-block ml-6">
            <select :value="pagination.limit" @change="(e) => handleFilterChange('limit', e, {as: 'number'})">
              <option>1</option>
              <option>2</option>
              <option>5</option>
            </select>
          </div>
          <div class="inline-flex border border-black p-2 m-6 gap-2">
            <button
              v-for="page in pages"
              @click="() => changePage(page)"
              :class="['border border-black size-8 flex justify-center items-center hover:bg-blue-500', pagination.page === page && 'font-black bg-blue-300']"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
