// Arquivo JavaScript da página Sobre o SIMPA

document.addEventListener('DOMContentLoaded', function() {

    console.log("Página Sobre o SIMPA carregada com sucesso!");

    // =============================
    // Hover nos cards dos residentes
    // =============================
    const residentCards = document.querySelectorAll('.residente-card');

    residentCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'scale(1.05)';
            card.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'scale(1)';
        });
    });


    // =============================
    // Hover nas logos dos parceiros
    // =============================
    const partnerLogos = document.querySelectorAll('.parceiros img');

    partnerLogos.forEach(logo => {
        logo.addEventListener('mouseenter', () => {
            logo.style.opacity = '0.8';
            logo.style.transition = 'opacity 0.3s ease';
        });

        logo.addEventListener('mouseleave', () => {
            logo.style.opacity = '1';
        });
    });


    // =============================
    // Links sociais abrindo em nova aba
    // =============================
    const socialLinks = document.querySelectorAll('.social a');

    socialLinks.forEach(link => {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
    });

});
