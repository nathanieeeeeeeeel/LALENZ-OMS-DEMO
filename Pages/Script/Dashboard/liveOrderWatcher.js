let lastOrderSnapshot = {};

async function checkOrderUpdates() {
  try {
    const res = await fetch(
      "../../Pages/Script/get_orders.php?_=" + Date.now(),
    );
    const orders = await res.json();

    if (!Array.isArray(orders)) return;

    orders.forEach((order) => {
      // first load
      if (!lastOrderSnapshot[order.id]) {
        lastOrderSnapshot[order.id] = order.status;
        return;
      }

      // status changed
      if (lastOrderSnapshot[order.id] !== order.status) {
        playNotificationSound();

        toastr.info(
          `<b>${order.customer_name}</b><br>
                     Order #${order.id} is now <b>${order.status}</b>`,
          "Kitchen Update",
        );

        lastOrderSnapshot[order.id] = order.status;

        // refresh dashboard
        loadStats();
      } else {
        // no change, just update snapshot
        lastOrderSnapshot[order.id] = order.status;
      }
    });
  } catch (err) {
    console.error(err);
  }
}

let audioUnlocked = false;

document.addEventListener(
  "click",
  async () => {
    const audio = document.getElementById("notificationSound");

    try {
      audio.muted = true;

      await audio.play();

      audio.pause();
      audio.currentTime = 0;
      audio.muted = false;

      audioUnlocked = true;
      console.log("Audio unlocked");
    } catch (err) {
      console.error(err);
    }
  },
  {
    once: true,
  },
);

function playNotificationSound() {
  if (!audioUnlocked) {
    console.log("Audio not unlocked yet.");
    return;
  }

  const audio = document.getElementById("notificationSound");

  audio.pause();
  audio.currentTime = 0;

  audio.play().catch((err) => console.error(err));
}
