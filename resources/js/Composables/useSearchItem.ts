import {MaybeRefOrGetter, ref, toValue, watchEffect} from "vue";

type Item = {
  key: number,
  label: string,
}

const employees: Item[] = [
  {key: 1, label: 'John'},
  {key: 2, label: 'Alice'},
  {key: 3, label: 'Brian'},
  {key: 4, label: 'Jason'},
  {key: 5, label: 'Michael'},
  {key: 6, label: 'Linda'},
  {key: 7, label: 'Sharon'},
  {key: 8, label: 'Laura'},
  {key: 13, label: 'Margaret'},
  {key: 14, label: 'Carol'},
  {key: 15, label: 'Stephen'},
  {key: 16, label: 'Justin'},
  {key: 9, label: 'Charles'},
  {key: 10, label: 'Kimberly'},
  {key: 11, label: 'Jacob'},
  {key: 12, label: 'Shirley'},
  {key: 17, label: 'Paul'},
  {key: 18, label: 'Kevin'},
  {key: 19, label: 'Nicole'},
  {key: 20, label: 'Anna'},
]

export function useSearchItem(item: MaybeRefOrGetter<Item | null>) {
  const search = ref(toValue(item)?.label ?? '')
  const suggestions = ref<Item[]>([])

  watchEffect(() => {
    const termValue = toValue(search)

    if (termValue === '') {
      suggestions.value = []
      return
    }

    suggestions.value = employees.filter(employee => employee.label.toLowerCase().includes(termValue.toLowerCase())).sort((a, b) => a.label.toLowerCase().localeCompare(b.label.toLowerCase()))
  })

  watchEffect(() => {
    search.value = toValue(item)?.label ?? ''
  })

  return {suggestions, search}
}
