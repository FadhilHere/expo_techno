<script setup lang="ts">
import type {
  ColumnFiltersState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Badge } from '@/components/ui/badge'
import {
  createColumnHelper,
  FlexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ArrowUpDown, ChevronDown, Eye, Clock, CheckCircle2, XCircle, AlertCircle } from 'lucide-vue-next'
import { h, ref, onMounted, onBeforeUnmount } from 'vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import Alert from '@/components/Alert.vue';

const alert = ref({
  show: false,
  type: 'success' as 'success' | 'error',
  message: ''
});

const page = usePage<CustomPageProps>();
if (page.props.flash?.success) {
  alert.value = {
    show: true,
    type: 'success',
    message: page.props.flash.success
  };
} else if (page.props.flash?.error) {
  alert.value = {
    show: true,
    type: 'error',
    message: page.props.flash.error
  };
}

// set up event listener for success events
const successHandler = () => {
  if (page.props.flash?.success) {
    alert.value = {
      show: true,
      type: 'success',
      message: page.props.flash.success
    };
  }
}

onMounted(() => {
  router.on('success', successHandler);
});

onBeforeUnmount(() => {
  // Use type assertion to avoid TypeScript error
  // This is safe because Inertia router does have an 'off' method
  (router as any).off?.('success', successHandler);
});

export interface PreOrder {
  id: string
  nama_pemesan: string
  nomor_wa: string
  status_pesanan: string
  catatan_tambahan: string
  created_at: string
}

export interface PreOrderDetail extends PreOrder {
  total: number
  items: {
    id: string
    nama_produk: string
    harga_satuan: number
    qty: number
    subtotal: number
  }[]
}

// Props definition first
const props = defineProps<{
  preOrders: PreOrder[]
}>()

// State refs
const selectedPreOrder = ref<PreOrderDetail | null>(null)
const isDetailDialogOpen = ref(false)
const isLoading = ref(false)
const filterInput = ref('')

// Fetch pre-order details
const fetchPreOrderDetail = async (id: string) => {
  isLoading.value = true
  try {
    const response = await axios.get(`/mahasiswa/pre-orders/${id}`)
    selectedPreOrder.value = response.data
    isDetailDialogOpen.value = true
  } catch (error) {
    console.error('Error fetching pre-order details:', error)
  } finally {
    isLoading.value = false
  }
}

// Update pre-order status
const updatePreOrderStatus = async (id: string, status: string) => {
  try {
    router.post(`/mahasiswa/pre-orders/${id}/status`,
      { status },
      {
        headers: { 'X-HTTP-Method-Override': 'PATCH' },
        onSuccess: function() {
          // Refresh the pre-order data
          if (selectedPreOrder.value && selectedPreOrder.value.id === id) {
            selectedPreOrder.value.status_pesanan = status;
          }
          // Show success alert
          alert.value = {
            show: true,
            type: 'success',
            message: 'Status pesanan berhasil diperbarui'
          };

          // Close the dialog
          isDetailDialogOpen.value = false;
        },
        onError: function(errors) {
          console.error('Update error:', errors);
          alert.value = {
            show: true,
            type: 'error',
            message: 'Terjadi kesalahan saat mengubah status pesanan'
          };
        }
      }
    );
  } catch (error) {
    console.error('Error updating pre-order status:', error);
  }
}

// Get status badge color
const getStatusBadge = (status: string) => {
  switch (status.toLowerCase()) {
    case 'confirmed':
      return { variant: 'success', icon: CheckCircle2 }
    case 'pending':
      return { variant: 'warning', icon: Clock }
    case 'canceled':
      return { variant: 'destructive', icon: XCircle }
    default:
      return { variant: 'secondary', icon: AlertCircle }
  }
}

const isStatusEditable = (status: string) => {
  return status.toLowerCase() !== 'confirmed'
}

const columnHelper = createColumnHelper<PreOrder>()

const columns = [
  columnHelper.accessor('nama_pemesan', {
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Nama Pemesan', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', {}, row.getValue('nama_pemesan')),
  }),
  columnHelper.accessor('nomor_wa', {
    header: 'Nomor WA',
    cell: ({ row }) => h('div', {}, row.getValue('nomor_wa')),
  }),
  columnHelper.accessor('status_pesanan', {
    header: 'Status',
    cell: ({ row }) => {
      const status = row.getValue('status_pesanan') as string
      const { variant, icon } = getStatusBadge(status)

      return h(Badge, { variant }, () => [
        h(icon, { class: 'mr-1 h-3 w-3' }),
        status
      ])
    },
  }),
  columnHelper.accessor('created_at', {
    header: 'Tanggal',
    cell: ({ row }) => h('div', {}, row.getValue('created_at')),
  }),
  columnHelper.display({
    id: 'actions',
    enableHiding: false,
    header: () => h('div', { class: 'text-right' }, 'Aksi'),
    cell: ({ row }) => {
      const preOrder = row.original

      return h('div', { class: 'flex justify-end space-x-2' }, [
        h(Button, {
          variant: 'outline',
          size: 'sm',
          onClick: () => fetchPreOrderDetail(preOrder.id),
        }, () => [h(Eye, { class: 'mr-1 h-4 w-4' }), 'Detail']),
      ])
    },
  }),
]

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})

// Apply column filter directly
const setFilterValue = (value) => {
  filterInput.value = value;
  table.getColumn('nama_pemesan')?.setFilterValue(value);
};

const table = useVueTable({
  get data() { return props.preOrders },
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  onSortingChange: (updater) => {
    sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater
  },
  onColumnFiltersChange: (updater) => {
    columnFilters.value = typeof updater === 'function' ? updater(columnFilters.value) : updater
  },
  onColumnVisibilityChange: (updater) => {
    columnVisibility.value = typeof updater === 'function' ? updater(columnVisibility.value) : updater
  },
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
  },
})
</script>

<template>
  <AuthLayout
    title="Daftar Pre-Order"
    description="Kelola pesanan dari pelanggan anda"
    :breadcrumbs="[
      { label: 'Dashboard', href: '/dashboard' },
      { label: 'Pre-Order', href: null }
    ]"
  >
    <div class="w-full">
      <div class="flex gap-2 items-center py-4">
        <Input
          class="max-w-sm"
          placeholder="Filter nama pemesan..."
          v-model="filterInput"
          @update:model-value="setFilterValue"
        />
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="ml-auto">
              Tampilan Kolom <ChevronDown class="ml-2 h-4 w-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuCheckboxItem
              v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
              :key="column.id"
              class="capitalize"
              :model-value="column.getIsVisible()"
              @update:model-value="(value) => {
                column.toggleVisibility(!!value)
              }"
            >
              {{ column.id }}
            </DropdownMenuCheckboxItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
              <TableHead class="w-12">No</TableHead>
              <TableHead
                v-for="header in headerGroup.headers" :key="header.id"
                :class="cn({ 'sticky bg-background/95': header.column.getIsPinned() })"
              >
                <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="table.getRowModel().rows?.length">
              <TableRow
                v-for="(row, rowIndex) in table.getRowModel().rows"
                :key="row.id"
              >
                <!-- Row number cell -->
                <TableCell class="text-center font-medium">
                  {{ table.getState().pagination.pageIndex * table.getState().pagination.pageSize + rowIndex + 1 }}
                </TableCell>
                <!-- Regular data cells -->
                <TableCell
                  v-for="cell in row.getVisibleCells()"
                  :key="cell.id"
                  :class="cn({ 'sticky bg-background/95': cell.column.getIsPinned() })"
                >
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
            </template>

            <TableRow v-else>
              <TableCell
                :colspan="columns.length + 1"
                class="h-24 text-center"
              >
                Tidak ada pre-order.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <div class="flex items-center justify-end space-x-2 py-4">
        <div class="flex-1 text-sm text-muted-foreground">
          Total {{ table.getFilteredRowModel().rows.length }} pesanan.
        </div>
        <div class="space-x-2">
          <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanPreviousPage()"
            @click="table.previousPage()"
          >
            Sebelumnya
          </Button>
          <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanNextPage()"
            @click="table.nextPage()"
          >
            Selanjutnya
          </Button>
        </div>
      </div>
    </div>

    <!-- Pre-Order Detail Dialog -->
    <Dialog v-model:open="isDetailDialogOpen">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Detail Pesanan</DialogTitle>
          <DialogDescription v-if="selectedPreOrder">
            Pesanan dari {{ selectedPreOrder.nama_pemesan }} pada {{ selectedPreOrder.created_at }}
          </DialogDescription>
        </DialogHeader>

        <div v-if="isLoading" class="flex justify-center py-4">
          <div class="animate-spin h-8 w-8 border-4 border-primary border-t-transparent rounded-full"></div>
        </div>

        <div v-else-if="selectedPreOrder" class="space-y-4">
          <!-- Basic Info -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-medium">Nama Pemesan</p>
              <p class="text-sm">{{ selectedPreOrder.nama_pemesan }}</p>
            </div>
            <div>
              <p class="text-sm font-medium">Nomor WA</p>
              <p class="text-sm">{{ selectedPreOrder.nomor_wa }}</p>
            </div>
            <div>
              <p class="text-sm font-medium">Status</p>
              <Badge :variant="getStatusBadge(selectedPreOrder.status_pesanan).variant">
                {{ selectedPreOrder.status_pesanan }}
              </Badge>
            </div>
            <div>
              <p class="text-sm font-medium">Tanggal</p>
              <p class="text-sm">{{ selectedPreOrder.created_at }}</p>
            </div>
          </div>

          <!-- Catatan -->
          <div v-if="selectedPreOrder.catatan_tambahan">
            <p class="text-sm font-medium">Catatan Tambahan</p>
            <p class="text-sm">{{ selectedPreOrder.catatan_tambahan }}</p>
          </div>

          <!-- Items Table -->
          <div>
            <p class="text-sm font-medium mb-2">Item Pesanan</p>
            <div class="border rounded-md">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Produk</TableHead>
                    <TableHead class="text-right">Harga</TableHead>
                    <TableHead class="text-right">Qty</TableHead>
                    <TableHead class="text-right">Subtotal</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="item in selectedPreOrder.items" :key="item.id">
                    <TableCell>{{ item.nama_produk }}</TableCell>
                    <TableCell class="text-right">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.harga_satuan) }}</TableCell>
                    <TableCell class="text-right">{{ item.qty }}</TableCell>
                    <TableCell class="text-right">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.subtotal) }}</TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
            <div class="flex justify-end mt-2">
              <div class="text-right">
                <p class="text-sm font-medium">Total</p>
                <p class="text-lg font-bold">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(selectedPreOrder.total) }}</p>
              </div>
            </div>
          </div>

          <!-- Update Status -->
          <div v-if="isStatusEditable(selectedPreOrder.status_pesanan)" class="flex justify-between pt-4 border-t">
            <div class="space-x-2">
              <Button
                v-if="selectedPreOrder.status_pesanan.toLowerCase() !== 'canceled'"
                variant="outline"
                size="sm"
                @click="updatePreOrderStatus(selectedPreOrder.id, 'canceled')"
              >
                <XCircle class="mr-1 h-4 w-4" /> Tolak Pesanan
              </Button>
            </div>
            <Button
              v-if="selectedPreOrder.status_pesanan.toLowerCase() === 'pending'"
              variant="default"
              size="sm"
              @click="updatePreOrderStatus(selectedPreOrder.id, 'confirmed')"
            >
              <CheckCircle2 class="mr-1 h-4 w-4" /> Konfirmasi Pesanan
            </Button>
          </div>
          <div v-else class="pt-4 border-t text-center">
            <p class="text-sm text-muted-foreground">Status pesanan sudah final dan tidak dapat diubah.</p>
          </div>
        </div>
      </DialogContent>
    </Dialog>

    <!-- Alert Component -->
    <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />
  </AuthLayout>
</template>
