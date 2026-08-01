const systemFolder="/" + window.location.pathname.split("/")[1];
let customerListData=[];

function openCustomerBook(){
    const modal=document.getElementById("customerBookModal");
    modal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");

    if(customerListData.length===0){
        loadCustomers();
    }else{
        renderCustomers(customerListData);
    }
}

function renderCustomers(customers){
    const list=document.getElementById("customerList");

    if(!customers.length){
        list.innerHTML=`
            <div class="p-6 text-center text-gray-500">
                No customers found.
            </div>
        `;
        return;
    }

    list.innerHTML=customers.map(customer=>`
        <div class="p-5 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <div class="flex justify-between items-center">

                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white">
                        ${customer.customer_name}
                    </h4>

                    <p class="text-sm text-gray-500">
                        ${customer.customer_address}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        ${customer.total_orders} orders
                        • Last order:
                        ${customer.last_order}
                    </p>
                </div>

                <button
                    onclick='selectCustomer(${JSON.stringify(customer)})'
                    class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold">
                    Select
                </button>

            </div>
        </div>
    `).join("");
}

function closeCustomerBook(){
    const modal=document.getElementById("customerBookModal");
    modal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
}

document.addEventListener("DOMContentLoaded",()=>{
    const modal=document.getElementById("customerBookModal");
    if(!modal)return;

    modal.addEventListener("click",e=>{
        if(e.target===modal) closeCustomerBook();
    });
});

document.addEventListener("keydown",e=>{
    if(e.key==="Escape"){
        const modal=document.getElementById("customerBookModal");
        if(modal&&!modal.classList.contains("hidden")){
            closeCustomerBook();
        }
    }
});

async function loadCustomers(){
    const list=document.getElementById("customerList");

    list.innerHTML=`
        <div class="p-6 text-center text-gray-500">
            Loading customers...
        </div>
    `;

    try{
        const response=await fetch(
            `${systemFolder}/Pages/Script/customers/fetchAll.php`
        );

        customerListData=await response.json();
        renderCustomers(customerListData);

    }catch(error){
        console.error(error);

        list.innerHTML=`
            <div class="p-6 text-center text-red-500">
                Failed to load customers.
            </div>
        `;
    }
}

function filterCustomers(){
    const keyword=document
        .getElementById("customerSearch")
        .value.toLowerCase().trim();

    renderCustomers(
        customerListData.filter(customer=>
            customer.customer_name.toLowerCase().includes(keyword) ||
            customer.customer_address.toLowerCase().includes(keyword)
        )
    );
}

function selectCustomer(customer){
    document.getElementById("custName").value=customer.customer_name;
    document.getElementById("custAddr").value=customer.customer_address;

    if(document.getElementById("custPhone")){
        document.getElementById("custPhone").value=customer.customer_phone||"";
    }

    closeCustomerBook();
    toastr.success("Customer details prefilled.");
}