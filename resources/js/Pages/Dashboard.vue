<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {computed} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

type Filter<TKey extends string = string> = {
  options: Record<TKey, string[]>
  selected: Record<TKey, string | undefined>
}

type Pagination = {
  page: number
  limit: number
  pages: number
  total: number
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
}

const props = defineProps<Props>()

const pages = computed(() => Array.from({length: props.pagination.pages}, (_, i) => i + 1))

const query = computed(() => ({
  ...props.filter.selected,
  page: props.pagination.page,
  limit: props.pagination.limit
}))

function updateQuery(key: string, value: unknown): void {
  router.get(route('dashboard'), {...query.value, [key]: value, page: 1}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

function resetFilter(): void {
  router.get(route('dashboard'), {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

function changePage(page: number): void {
  router.get(route('dashboard'), {...query.value, page}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

function handleFilterChange(key: string, e: Event): void {
  if (e.target && 'value' in e.target && typeof e.target.value === 'string') {
    updateQuery(key, e.target.value)
  }
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
          <div class="p-6 text-gray-900">
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
            <select :value="pagination.limit" @change="(e) => handleFilterChange('limit', e)">
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
