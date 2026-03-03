// Espaço para futuras interações
console.log("Página institucional SIMPA carregada");
// Arquivo JavaScript da página Sobre o SIMPA
console.log("Página Sobre carregada com sucesso!");

// Aguarda o DOM ser completamente carregado
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM totalmente carregado");
});
// Animações para página Sobre o SIMPA
document.addEventListener('DOMContentLoaded', function() {
    
    // Efeito de hover nos cards dos residentes
    const residentCards = document.querySelectorAll('.residente-card');
    residentCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Efeito de hover nas logos dos parceiros
    const partnerLogos = document.querySelectorAll('.parceiros img');
    partnerLogos.forEach(logo => {
        logo.addEventListener('mouseenter', function() {
            this.style.opacity = '0.8';
            this.style.transition = 'opacity 0.3s ease';
        });
        
        logo.addEventListener('mouseleave', function() {
            this.style.opacity = '1';
        });
    });
});
// Abrir links sociais em nova aba
document.addEventListener('DOMContentLoaded', function() {
    const socialLinks = document.querySelectorAll('.social a');
    socialLinks.forEach(link => {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
    });
});