let menuMap = {};

// Load menu items
fetch('./../../Pages/Script/api/items/get.php?t=' + Date.now())
    .then(res => res.json())
    .then(data => {
        data.items.forEach(item => {
            menuMap[item.id] = item.name;
        });
    })
    .catch(err => console.error("Failed to load items:", err));

async function downloadReceipt(orderId, currencySymbol = "₱") {
    const systemInfo = await fetch(`./../../Pages/Script/get_system.php`).then(res => res.json());
    /*
    * Receipt generation and download handler (client-side).
    * Builds a printable HTML receipt from order modal data,
    * enriches it with system settings (logo, name, address),
    * and converts it into an image using html2canvas for download.
    * Includes support for scheduled orders, item parsing,
    * and dynamic formatting of totals and payment details.
    * Used in Pages/admin/dashboard.php for order receipt export.
    */

    const orderData = await fetch(`./../../Pages/Script/get_order_details.php?id=${orderId}`).then(res => res.json());
    console.log("=== DEBUG: Receipt Generation Started ===");
    console.log("Data received:", orderData);
    console.log("System info fetched:", systemInfo);
    let {
        system_name,
        system_logo,
        currency_code,
        receipt_title,
        receipt_subtitle,
        receipt_address,
        receipt_footer
    } = systemInfo || {};

    const modal = document.getElementById("customerModal");

    // Get logo from link tag
    const logo = $("link[rel='icon']").attr("href") || "./../Assets/logo.png";
    console.log("Logo URL:", logo);
    // 1. Hide the context menu
    if (!document.getElementById("contextMenu").classList.contains("hidden")) {
        document.getElementById("contextMenu").classList.add("hidden");
    };

    /* ================= 1.1. DATA SCRAPING ================= */
    const customerName = orderData.customer_name || "Customer";
    const customerAddr = orderData.customer_address || "N/A";

    const totalAmount = orderData.grand_total || `${currencySymbol}0.00`;
    const subtotal = orderData.subtotal || `${currencySymbol}0.00`;
    const discount = orderData.discount || `${currencySymbol}0.00`;
    const deliveryFee = orderData.delivery_fee || `${currencySymbol}0.00`;
    const advancePayment = orderData.advance_payment || `${currencySymbol}0.00`;
    const paymentMethod = orderData.payment_method || "Cash";
    const orderDate = orderData.order_datetime;
    
    console.log("Modal Data:", { customerName, customerAddr, totalAmount, paymentMethod, orderId, orderDate });

    
    // Get scheduled date if available
    const isScheduled = orderData?.is_scheduled || false;
    const scheduledDate = orderData.scheduled_datetime || null;
    
    console.log("Date Info:", { orderDate, isScheduled, scheduledDate });
    
    if (isScheduled == true && scheduledDate != null) {
        try {
            scheduledDate = moment(scheduledDate).format("llll");
            console.log("Formatted Scheduled Date:", scheduledDate);
        } catch (e) {
            console.error("Error formatting scheduled date:", e);
        }
    }

    let itemsParsed = [];


    // API array format
    if (Array.isArray(orderData.items) && orderData.items.length) {

        itemsParsed = orderData.items.map(item => ({
            id: item.id || null,
            name: item.name || menuMap[item.id] || `Item #${item.id}` || "Unknown Item",
            qty: Number(item.qty || item.quantity || 1),
            unitPrice: item.price || item.unitPrice || `${currencySymbol}0.00`,
            amnt: item.total || item.amount || item.price || `${currencySymbol}0.00`
        }));

    }


    // JSON string order_items format
    else if (orderData.order_items) {

        try {

            const parsedItems =
                typeof orderData.order_items === "string"
                    ? JSON.parse(orderData.order_items)
                    : orderData.order_items;

            console.log("PARSING ORDER ITEMS:", parsedItems);

            itemsParsed = parsedItems.map(item => {
                console.log("Parsed data", item);

                const qty = Number(item.qty || item.quantity || 1);

                const amount = Number(
                    item.amnt ??
                    item.amount ??
                    item.total ??
                    item.price ??
                    0
                );

                // calculate unit price if only total amount exists
                const unitPrice = amount / qty;

                return {
                    name: menuMap[item.id] || "Item",
                    qty: qty,
                    unitPrice: `${currencySymbol}${unitPrice.toFixed(2)}`,
                    amnt: `${currencySymbol}${amount.toFixed(2)}`
                };
            });

        } catch(e) {
            console.error("order_items parsing failed:", e);
        }

    }

    console.log("Receipt Items:", itemsParsed);

    const parseNumber = (value) => {
        const cleaned = String(value || "").replace(/[^0-9.\-]/g, "");
        return parseFloat(cleaned) || 0;
    };

    const subtotalValue = parseNumber(subtotal);
    const discountValue = parseNumber(discount);
    const deliveryFeeValue = parseNumber(deliveryFee);
    const advancePaymentValue = parseNumber(advancePayment);

    const hasBreakdown = discountValue !== 0 || deliveryFeeValue !== 0 || advancePaymentValue !== 0;
    const breakdownRows = [];

    if (hasBreakdown) {
        breakdownRows.push(`
            <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 10px;">
                <span style="font-weight: bold; color: #000000;">Subtotal</span>
                <span style="font-weight: bold; color: #000000;">${currencySymbol}${(Number(subtotal) || 0).toLocaleString("en-PH", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })}
                </span>
            </div>
        `);
    }

    if (discountValue !== 0) {
        breakdownRows.push(`
            <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 10px;">
                <span style="font-weight: bold; color: #000000;">Discount</span>
                <span style="font-weight: bold; color: #000000;">${currencySymbol}${(Number(discount) || 0).toLocaleString("en-PH", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</span>
            </div>
        `);
    }
    if (deliveryFeeValue !== 0) {
        breakdownRows.push(`
            <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 10px;">
                <span style="font-weight: bold; color: #000000;">Delivery Fee</span>
                <span style="font-weight: bold; color: #000000;">${currencySymbol}${(Number(deliveryFee) || 0).toLocaleString("en-PH", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</span>
            </div>
        `);
    }
    if (advancePaymentValue !== 0) {
        breakdownRows.push(`
            <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 10px;">
                <span style="font-weight: bold; color: #000000;">Advance Payment</span>
                <span style="font-weight: bold; color: #000000;">${currencySymbol}${(Number(advancePayment) || 0).toLocaleString("en-PH", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</span>
            </div>
        `);
    }

    const breakdownSection = hasBreakdown ? `
        <!-- Payment Summary -->
        <div style="width: 100%; max-width: 480px; margin-left: auto; margin-bottom: 40px;">
            <div style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px;">Payment Summary</div>
            ${breakdownRows.join("")}
        </div>
    ` : "";

    // Use passed in system address or fallback text
    const systemAddress = systemInfo?.systemAddress || "(Your Business Address Here)";

    /* ================= 2. CREATE HTML RECEIPT ================= */
    const receiptHTML = `
        <div id="receipt-container" style="
            width: 1275px;
            height: 1800px;
            background: white;
            font-family: Arial, sans-serif;
            position: relative;
            padding: 60px;
            box-sizing: border-box;
            display: none;
        ">
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 60px;">
                <div id="logo-container" style="display:flex; justify-content:center; margin-bottom: 20px;">
                    <img src="${logo}" alt="Logo" width="150" height="150" style="max-width: 100%; max-height: 100%;">
                </div>
                <div style="font-size: 36px; font-weight: bold; color: #111827;">${systemInfo?.systemName || "LALENZ FOODIES"}</div>
                <div style="font-size: 14px; color: #4B5563;">${receipt_address || "(Your Business Address Here)"}</div>
                <div style="font-size: 32px; font-weight: bold; color: #111827; margin-top: 10px;">ORDER SLIP</div>
            </div>

            <!-- Info Bar -->
            <div style="
                background: #F3F4F6;
                padding: 30px;
                margin-bottom: 60px;
                display: flex;
                justify-content: space-between;
            ">
                <div style="flex: 1;">
                    <div style="font-size: 20px; font-weight: bold; color: #374151; margin-bottom: 15px;">
                        CUSTOMER NAME:<br><span class="text-emerald-600">${customerName.toUpperCase()}</span>
                    </div>
                    <div style="font-size: 20px; font-weight: bold; color: #374151; margin-bottom: 15px;">
                        ADDRESS:<br><span class="text-emerald-600">${customerAddr}</span>
                    </div>
                </div>
                <div style="text-align: left; flex: 1;">
                    <div style="font-size: 20px; font-weight: bold; color: #374151; margin-bottom: 15px;">
                        ORDER REF. NO.:<br><span class="text-emerald-600">#${String(orderId).padStart(3, '0')}</span>
                    </div>
                    ${isScheduled == true && scheduledDate != null ? 
                        `<div style="font-size: 20px; font-weight: bold; color: #374151;">
                            ORDER DATE:<br><span class="text-emerald-600">${moment(orderDate).format("ddd, MMM DD, YYYY • hh:mm A")}</span>
                        </div>
                        <div style="font-size: 20px; font-weight: bold; color: #374151; margin-bottom: 15px;">
                            SCHEDULED:<br><span class="text-emerald-600">${moment(scheduledDate).format("ddd, MMM DD, YYYY • hh:mm A")}</span>
                        </div>` :
                        `<div style="font-size: 20px; font-weight: bold; color: #374151;">
                            ORDER DATE:<br><span class="text-emerald-600">${moment(orderDate).format("ddd, MMM DD, YYYY • hh:mm A")}</span>
                        </div>`
                    }
                </div>
            </div>

            <!-- Items Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 60px; font-size: 18px;">
                <thead>
                    <tr style="border-bottom: 2px solid #E5E7EB;">
                        <th style="font-weight: bold; color: #111827; text-align: left; padding: 10px 0; width: 10%;">QTY</th>
                        <th style="font-weight: bold; color: #111827; text-align: left; padding: 10px 0; width: 40%;">DESCRIPTION</th>
                        <th style="font-weight: bold; color: #111827; text-align: right; padding: 10px 0; width: 25%;">PRICE</th>
                        <th style="font-weight: bold; color: #111827; text-align: right; padding: 10px 0; width: 25%;">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsParsed.length > 0 ? itemsParsed.slice(0, 6).map(item => `
                        <tr>
                            <td style="color: #111827; padding: 12px 0; text-align: left;">${item.qty}x</td>
                            <td style="color: #111827; padding: 12px 0; text-align: left;">${item.name}</td>
                            <td style="color: #6B7280; padding: 12px 0; text-align: right;">${item.unitPrice}</td>
                            <td style="color: #111827; font-weight: bold; padding: 12px 0; text-align: right;">${item.amnt}</td>
                        </tr>
                    `).join('') : '<tr><td colspan="4" style="text-align: center; padding: 20px;">No items</td></tr>'}
                </tbody>
            </table>

            ${breakdownSection}

            <!-- Total -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-top: 20px; border-top: 2px solid #E5E7EB;">
                <div style="font-size: 28px; font-weight: bold; color: #111827;">TOTAL :</div>
                <div style="font-size: 32px; font-weight: bold; color: #059669;">${currencySymbol}${totalAmount}</div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; font-size: 18px; color: #374151;">
                <div style="font-weight: 700;">Payment Method</div>
                <div style="font-weight: 700; color: #111827;">${paymentMethod.toUpperCase()}</div>
            </div>

            <!-- Footer -->
            <div style="text-align: center; font-size: 14px; color: #9CA3AF; font-weight: bold; margin-top: 40px;">
                NOT VALID FOR CLAIM OF INPUT TAX
            </div>
        </div>
    `;

    console.log("Receipt HTML created, length:", receiptHTML.length);

    // Append to body
    document.body.insertAdjacentHTML('beforeend', receiptHTML);
    const receiptElement = document.getElementById('receipt-container');

    // Temporarily show the element for capture
    receiptElement.style.display = 'block';
    receiptElement.style.position = 'absolute';
    receiptElement.style.left = '-9999px';
    receiptElement.style.top = '0';

    // Wait for images to load
    const images = receiptElement.querySelectorAll('img');
    console.log("Images to load:", images.length);
    
    const promises = Array.from(images).map((img, idx) => {
        return new Promise(resolve => {
            if (img.complete) {
                console.log(`Image ${idx} already loaded`);
                resolve();
            } else {
                img.onload = () => {
                    console.log(`Image ${idx} loaded successfully`);
                    resolve();
                };
                img.onerror = () => {
                    console.warn(`Image ${idx} failed to load`);
                    resolve(); // Still resolve to continue
                };
            }
        });
    });

    // If no images, proceed immediately
    if (promises.length === 0) {
        console.log("No images to wait for");
        promises.push(Promise.resolve());
    }

    Promise.all(promises).then(() => {
        console.log("All images loaded, starting html2canvas capture...");
        
        // Use html2canvas to capture
        html2canvas(receiptElement, {
            width: 1275,
            height: 1800,
            scale: 1,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            console.log("Canvas captured successfully, size:", canvas.width, "x", canvas.height);
            
            const link = document.createElement("a");
            link.download = `OrderSlip_${String(orderId).padStart(3, '0')}.jpg`;
            link.href = canvas.toDataURL("image/jpeg", 0.95);
            link.click();
            
            console.log("File download triggered");

            // Remove the element
            receiptElement.remove();
            console.log("Receipt container removed");
        }).catch(err => {
            console.error('Error generating receipt:', err);
            alert('Error generating receipt: ' + err.message);
            receiptElement.remove();
        });
    }).catch(err => {
        console.error('Error waiting for images:', err);
        receiptElement.remove();
    });
}