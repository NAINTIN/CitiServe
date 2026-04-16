document.querySelectorAll(".auth-toggle-password").forEach((button) => {
    button.addEventListener("click", () => {
        const targetId = button.getAttribute("data-target");
        const input = document.getElementById(targetId);
        if (!input) {
            return;
        }

        const showing = input.type === "text";
        input.type = showing ? "password" : "text";
        button.textContent = showing ? "Show" : "Hide";
    });
});
