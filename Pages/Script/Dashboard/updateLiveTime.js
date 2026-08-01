// 1. Live Clock
function updateLiveTime() {
const now = new Date();
    document.querySelector(".Date").innerText = now.toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
    });
}
setInterval(updateLiveTime, 1000);
updateLiveTime();