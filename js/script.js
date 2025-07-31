// 全局变量
let currentPage = 1;
let totalPages = 1;
let totalRecords = 0;
let pageSize = 20;
let allOrders = [];
let filteredOrders = [];
let currentSort = { field: '', direction: '' };
let currentEditId = null;

// 模拟订单数据
const mockOrders = [
    {
        id: 1,
        orderNumber: 'ORD-2024-001',
        customerName: '张三',
        customerPhone: '13812345678',
        customerAddress: '北京市朝阳区三里屯街道100号',
        productName: 'iPhone 15 Pro',
        productPrice: 8999.00,
        quantity: 1,
        totalAmount: 8999.00,
        status: 'pending',
        orderDate: '2024-01-15 10:30:00',
        notes: '客户要求尽快发货'
    },
    {
        id: 2,
        orderNumber: 'ORD-2024-002',
        customerName: '李四',
        customerPhone: '13987654321',
        customerAddress: '上海市浦东新区陆家嘴金融贸易区88号',
        productName: 'MacBook Pro 14',
        productPrice: 15999.00,
        quantity: 1,
        totalAmount: 15999.00,
        status: 'processing',
        orderDate: '2024-01-15 14:20:00',
        notes: '教育优惠订单'
    },
    {
        id: 3,
        orderNumber: 'ORD-2024-003',
        customerName: '王五',
        customerPhone: '13611223344',
        customerAddress: '广州市天河区珠江新城CBD中心区168号',
        productName: 'iPad Air',
        productPrice: 4599.00,
        quantity: 2,
        totalAmount: 9198.00,
        status: 'shipped',
        orderDate: '2024-01-14 16:45:00',
        notes: '公司采购'
    },
    {
        id: 4,
        orderNumber: 'ORD-2024-004',
        customerName: '赵六',
        customerPhone: '13555667788',
        customerAddress: '深圳市南山区科技园南区深南大道9999号',
        productName: 'AirPods Pro',
        productPrice: 1899.00,
        quantity: 3,
        totalAmount: 5697.00,
        status: 'delivered',
        orderDate: '2024-01-14 09:15:00',
        notes: '生日礼物'
    },
    {
        id: 5,
        orderNumber: 'ORD-2024-005',
        customerName: '钱七',
        customerPhone: '13899887766',
        customerAddress: '杭州市西湖区文三路269号',
        productName: 'Apple Watch Ultra',
        productPrice: 6299.00,
        quantity: 1,
        totalAmount: 6299.00,
        status: 'cancelled',
        orderDate: '2024-01-13 11:30:00',
        notes: '客户取消订单'
    }
];

// 生成更多模拟数据
function generateMockData() {
    const customers = ['张三', '李四', '王五', '赵六', '钱七', '孙八', '周九', '吴十', '郑十一', '王十二'];
    const products = [
        { name: 'iPhone 15 Pro', price: 8999 },
        { name: 'MacBook Pro 14', price: 15999 },
        { name: 'iPad Air', price: 4599 },
        { name: 'AirPods Pro', price: 1899 },
        { name: 'Apple Watch Ultra', price: 6299 },
        { name: 'iMac 24', price: 13999 },
        { name: 'Mac Studio', price: 19999 },
        { name: 'Studio Display', price: 11999 }
    ];
    const statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    const addresses = [
        '北京市朝阳区三里屯街道100号',
        '上海市浦东新区陆家嘴金融贸易区88号',
        '广州市天河区珠江新城CBD中心区168号',
        '深圳市南山区科技园南区深南大道9999号',
        '杭州市西湖区文三路269号',
        '成都市锦江区春熙路188号',
        '重庆市渝中区解放碑步行街66号',
        '西安市雁塔区高新路99号'
    ];

    for (let i = 6; i <= 200; i++) {
        const customer = customers[Math.floor(Math.random() * customers.length)];
        const product = products[Math.floor(Math.random() * products.length)];
        const quantity = Math.floor(Math.random() * 5) + 1;
        const status = statuses[Math.floor(Math.random() * statuses.length)];
        const address = addresses[Math.floor(Math.random() * addresses.length)];
        
        const date = new Date();
        date.setDate(date.getDate() - Math.floor(Math.random() * 30));
        
        mockOrders.push({
            id: i,
            orderNumber: `ORD-2024-${i.toString().padStart(3, '0')}`,
            customerName: customer,
            customerPhone: `138${Math.floor(Math.random() * 100000000).toString().padStart(8, '0')}`,
            customerAddress: address,
            productName: product.name,
            productPrice: product.price,
            quantity: quantity,
            totalAmount: product.price * quantity,
            status: status,
            orderDate: formatDateTime(date),
            notes: Math.random() > 0.7 ? '备注信息' : ''
        });
    }
}

// 格式化日期时间
function formatDateTime(date) {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// 格式化货币
function formatCurrency(amount) {
    return `¥${amount.toFixed(2)}`;
}

// 获取状态显示文本
function getStatusText(status) {
    const statusMap = {
        'pending': '待处理',
        'processing': '处理中',
        'shipped': '已发货',
        'delivered': '已送达',
        'cancelled': '已取消'
    };
    return statusMap[status] || status;
}

// 初始化
document.addEventListener('DOMContentLoaded', function() {
    generateMockData();
    allOrders = [...mockOrders];
    filteredOrders = [...allOrders];
    
    initializeEventListeners();
    loadOrderList();
    generateOrderNumber();
    
    // 设置默认日期
    const today = new Date();
    const startDate = new Date(today.getFullYear(), today.getMonth(), 1);
    document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
});

// 初始化事件监听器
function initializeEventListeners() {
    // 侧边栏切换
    document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
    
    // 页面切换
    document.querySelectorAll('[data-page]').forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            switchPage(page);
        });
    });
    
    // 筛选器
    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    document.getElementById('resetFilters').addEventListener('click', resetFilters);
    
    // 全选复选框
    document.getElementById('selectAll').addEventListener('change', toggleSelectAll);
    
    // 分页
    document.getElementById('prevPage').addEventListener('click', () => changePage(currentPage - 1));
    document.getElementById('nextPage').addEventListener('click', () => changePage(currentPage + 1));
    
    // 表单提交
    document.getElementById('saveOrder').addEventListener('click', saveOrder);
    
    // 添加商品
    document.getElementById('addProduct').addEventListener('click', addProductItem);
    
    // 搜索
    document.getElementById('globalSearch').addEventListener('input', debounce(globalSearch, 300));
    
    // 表单计算
    document.addEventListener('input', function(e) {
        if (e.target.name === 'productPrice' || e.target.name === 'productQuantity') {
            calculateSubtotal(e.target);
        }
    });
    
    // 表格排序
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const field = this.getAttribute('data-field');
            sortTable(field);
        });
    });
}

// 切换侧边栏
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
}

// 页面切换
function switchPage(pageName) {
    // 隐藏所有页面
    document.querySelectorAll('.page').forEach(page => {
        page.classList.add('hidden');
    });
    
    // 显示目标页面
    const targetPage = document.getElementById(pageName + 'Page');
    if (targetPage) {
        targetPage.classList.remove('hidden');
    }
    
    // 更新导航状态
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const activeNav = document.querySelector(`[data-page="${pageName}"]`).closest('.nav-item');
    if (activeNav) {
        activeNav.classList.add('active');
    }
    
    // 如果切换到编辑页面，重置表单
    if (pageName === 'edit') {
        resetOrderForm();
        generateOrderNumber();
    }
}

// 加载订单列表
function loadOrderList() {
    calculatePagination();
    renderOrderTable();
    renderPagination();
}

// 计算分页
function calculatePagination() {
    totalRecords = filteredOrders.length;
    totalPages = Math.ceil(totalRecords / pageSize);
    
    if (currentPage > totalPages) {
        currentPage = Math.max(1, totalPages);
    }
    
    // 更新分页信息
    document.getElementById('totalRecords').textContent = totalRecords;
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent = totalPages;
}

// 渲染订单表格
function renderOrderTable() {
    const tbody = document.getElementById('ordersTableBody');
    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = Math.min(startIndex + pageSize, filteredOrders.length);
    const pageOrders = filteredOrders.slice(startIndex, endIndex);
    
    if (pageOrders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11" style="text-align: center; padding: 40px;">暂无数据</td></tr>';
        return;
    }
    
    tbody.innerHTML = pageOrders.map(order => `
        <tr>
            <td><input type="checkbox" value="${order.id}"></td>
            <td class="tooltip" data-tooltip="${order.orderNumber}">${order.orderNumber}</td>
            <td class="tooltip" data-tooltip="${order.customerName}">${order.customerName}</td>
            <td class="tooltip" data-tooltip="${order.productName}">${order.productName}</td>
            <td>${order.quantity}</td>
            <td>${formatCurrency(order.productPrice)}</td>
            <td><strong>${formatCurrency(order.totalAmount)}</strong></td>
            <td><span class="status-badge status-${order.status}">${getStatusText(order.status)}</span></td>
            <td class="tooltip" data-tooltip="${order.orderDate}">${order.orderDate.split(' ')[0]}</td>
            <td class="tooltip" data-tooltip="${order.notes || '无'}">${order.notes ? order.notes.substring(0, 10) + '...' : '无'}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-primary" onclick="editOrder(${order.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="viewOrder(${order.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteOrder(${order.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// 渲染分页
function renderPagination() {
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    // 更新按钮状态
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
    
    // 生成页码
    let pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages, start + maxVisible - 1);
    
    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    pageNumbers.innerHTML = pages.map(page => `
        <a href="#" class="page-number ${page === currentPage ? 'active' : ''}" 
           onclick="changePage(${page}); return false;">${page}</a>
    `).join('');
}

// 切换页面
function changePage(page) {
    if (page >= 1 && page <= totalPages && page !== currentPage) {
        currentPage = page;
        loadOrderList();
    }
}

// 应用筛选器
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const minAmount = parseFloat(document.getElementById('minAmount').value) || 0;
    const maxAmount = parseFloat(document.getElementById('maxAmount').value) || Infinity;
    
    filteredOrders = allOrders.filter(order => {
        // 状态筛选
        if (status && order.status !== status) return false;
        
        // 日期筛选
        const orderDate = new Date(order.orderDate);
        if (startDate && orderDate < new Date(startDate)) return false;
        if (endDate && orderDate > new Date(endDate + ' 23:59:59')) return false;
        
        // 金额筛选
        if (order.totalAmount < minAmount || order.totalAmount > maxAmount) return false;
        
        return true;
    });
    
    currentPage = 1;
    loadOrderList();
}

// 重置筛选器
function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('minAmount').value = '';
    document.getElementById('maxAmount').value = '';
    
    filteredOrders = [...allOrders];
    currentPage = 1;
    loadOrderList();
}

// 全选/取消全选
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('#ordersTableBody input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// 全局搜索
function globalSearch() {
    const keyword = document.getElementById('globalSearch').value.toLowerCase();
    
    if (!keyword) {
        filteredOrders = [...allOrders];
    } else {
        filteredOrders = allOrders.filter(order => 
            order.orderNumber.toLowerCase().includes(keyword) ||
            order.customerName.toLowerCase().includes(keyword) ||
            order.productName.toLowerCase().includes(keyword) ||
            order.customerPhone.includes(keyword)
        );
    }
    
    currentPage = 1;
    loadOrderList();
}

// 防抖函数
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 表格排序
function sortTable(field) {
    const header = document.querySelector(`[data-field="${field}"]`);
    
    // 移除其他排序状态
    document.querySelectorAll('.sortable').forEach(h => {
        if (h !== header) {
            h.classList.remove('asc', 'desc');
        }
    });
    
    // 切换排序方向
    if (currentSort.field === field) {
        if (currentSort.direction === 'asc') {
            currentSort.direction = 'desc';
            header.classList.remove('asc');
            header.classList.add('desc');
        } else {
            currentSort.direction = 'asc';
            header.classList.remove('desc');
            header.classList.add('asc');
        }
    } else {
        currentSort.field = field;
        currentSort.direction = 'asc';
        header.classList.add('asc');
    }
    
    // 执行排序
    filteredOrders.sort((a, b) => {
        let valueA = a[field];
        let valueB = b[field];
        
        // 处理不同数据类型
        if (field === 'totalAmount' || field === 'productPrice') {
            valueA = parseFloat(valueA);
            valueB = parseFloat(valueB);
        } else if (field === 'orderDate') {
            valueA = new Date(valueA);
            valueB = new Date(valueB);
        } else {
            valueA = valueA.toString().toLowerCase();
            valueB = valueB.toString().toLowerCase();
        }
        
        if (valueA < valueB) {
            return currentSort.direction === 'asc' ? -1 : 1;
        }
        if (valueA > valueB) {
            return currentSort.direction === 'asc' ? 1 : -1;
        }
        return 0;
    });
    
    loadOrderList();
}

// 编辑订单
function editOrder(id) {
    const order = allOrders.find(o => o.id === id);
    if (!order) return;
    
    currentEditId = id;
    
    // 填充表单
    document.getElementById('orderNumber').value = order.orderNumber;
    document.getElementById('orderDate').value = order.orderDate.replace(' ', 'T');
    document.getElementById('customerName').value = order.customerName;
    document.getElementById('customerPhone').value = order.customerPhone;
    document.getElementById('customerAddress').value = order.customerAddress;
    document.getElementById('productName').value = order.productName;
    document.getElementById('productPrice').value = order.productPrice;
    document.getElementById('productQuantity').value = order.quantity;
    document.getElementById('productSubtotal').value = formatCurrency(order.totalAmount);
    document.getElementById('orderStatus').value = order.status;
    document.getElementById('totalAmount').value = formatCurrency(order.totalAmount);
    document.getElementById('orderNotes').value = order.notes;
    
    document.getElementById('editTitle').textContent = '编辑订单';
    switchPage('edit');
}

// 查看订单
function viewOrder(id) {
    editOrder(id);
    // 可以添加只读模式的逻辑
}

// 删除订单
function deleteOrder(id) {
    if (confirm('确定要删除这个订单吗？')) {
        const index = allOrders.findIndex(o => o.id === id);
        if (index > -1) {
            allOrders.splice(index, 1);
            filteredOrders = filteredOrders.filter(o => o.id !== id);
            loadOrderList();
        }
    }
}

// 保存订单
function saveOrder() {
    const form = document.getElementById('orderForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const orderData = {
        orderNumber: document.getElementById('orderNumber').value,
        orderDate: document.getElementById('orderDate').value.replace('T', ' ') + ':00',
        customerName: document.getElementById('customerName').value,
        customerPhone: document.getElementById('customerPhone').value,
        customerAddress: document.getElementById('customerAddress').value,
        productName: document.getElementById('productName').value,
        productPrice: parseFloat(document.getElementById('productPrice').value),
        quantity: parseInt(document.getElementById('productQuantity').value),
        totalAmount: parseFloat(document.getElementById('productPrice').value) * parseInt(document.getElementById('productQuantity').value),
        status: document.getElementById('orderStatus').value,
        notes: document.getElementById('orderNotes').value
    };
    
    if (currentEditId) {
        // 更新现有订单
        const index = allOrders.findIndex(o => o.id === currentEditId);
        if (index > -1) {
            allOrders[index] = { ...allOrders[index], ...orderData };
        }
    } else {
        // 创建新订单
        const newId = Math.max(...allOrders.map(o => o.id)) + 1;
        allOrders.push({ id: newId, ...orderData });
    }
    
    // 更新筛选后的数据
    applyFilters();
    
    alert('订单保存成功！');
    switchPage('list');
}

// 重置订单表单
function resetOrderForm() {
    document.getElementById('orderForm').reset();
    currentEditId = null;
    document.getElementById('editTitle').textContent = '新建订单';
    
    // 设置默认值
    const now = new Date();
    document.getElementById('orderDate').value = now.toISOString().slice(0, 16);
    calculateSubtotal(document.getElementById('productPrice'));
}

// 生成订单号
function generateOrderNumber() {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');
    const timestamp = now.getTime().toString().slice(-4);
    
    const orderNumber = `ORD-${year}${month}${day}-${timestamp}`;
    document.getElementById('orderNumber').value = orderNumber;
}

// 计算小计
function calculateSubtotal(element) {
    const productItem = element.closest('.product-item');
    if (!productItem) return;
    
    const price = parseFloat(productItem.querySelector('[name="productPrice"]').value) || 0;
    const quantity = parseInt(productItem.querySelector('[name="productQuantity"]').value) || 0;
    const subtotal = price * quantity;
    
    productItem.querySelector('#productSubtotal').value = formatCurrency(subtotal);
    
    // 更新总金额
    calculateTotalAmount();
}

// 计算总金额
function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('.product-item').forEach(item => {
        const price = parseFloat(item.querySelector('[name="productPrice"]').value) || 0;
        const quantity = parseInt(item.querySelector('[name="productQuantity"]').value) || 0;
        total += price * quantity;
    });
    
    document.getElementById('totalAmount').value = formatCurrency(total);
}

// 添加商品项
function addProductItem() {
    const container = document.querySelector('.products-container');
    const productCount = container.children.length;
    
    const productItem = document.createElement('div');
    productItem.className = 'product-item';
    productItem.innerHTML = `
        <button type="button" class="remove-product" onclick="removeProductItem(this)">
            <i class="fas fa-times"></i>
        </button>
        <div class="form-row">
            <div class="form-group">
                <label>商品名称 *</label>
                <input type="text" name="productName" required>
            </div>
            <div class="form-group">
                <label>单价 *</label>
                <input type="number" name="productPrice" step="0.01" required>
            </div>
            <div class="form-group">
                <label>数量 *</label>
                <input type="number" name="productQuantity" min="1" required>
            </div>
            <div class="form-group">
                <label>小计</label>
                <input type="text" readonly>
            </div>
        </div>
    `;
    
    container.appendChild(productItem);
}

// 移除商品项
function removeProductItem(button) {
    const productItem = button.closest('.product-item');
    productItem.remove();
    calculateTotalAmount();
}

// 响应式处理
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.remove('show');
    }
});

