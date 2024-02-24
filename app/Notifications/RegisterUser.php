<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegisterUser extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Knižnica KUS Jána Kollára')
            ->greeting('Vítame Vás ' . $this->user->name)
            ->line('Sme veľmi radi, že ste sa rozhodli stať sa členom našej knižnice :).')
            ->line('Pomocou našej webovej aplikácie sa môžete jednoducho a rýchlo zorientovať v našej knižnici. Ponúkame Vám rôzne knihy rôznych žánrov a kategórií.')

            ->line('----------------------------------------------------------')

            ->line('Na samom začiatku Vás poprosíme o zmenu Vášho hesla v našej aplikácii kvôli bezpečnosti.')
            ->line('Urobíte to nasledovným spôsobom:')
            ->line('1. Prihláste sa do našej aplikácie [kniznica.kusjanakollara.org](https://kniznica.kusjanakollara.org) pomocou prihlasovacích údajov:')
            ->line('- Email: ' . $this->user->email)
            ->line('- Heslo: ' . $this->user->email)
            ->line('2. Ak sa prihlasujete cez mobil, stlačte vpravo hore na Vaše meno a priezvisko, aby sa Vám otvoril používateľský panel z pravej strany (pokiaľ sa prihlasujete cez počítač, tento panel je otvorený).')
            ->line('3. Stlačte na "Profil".')
            ->line('4. Stlačte žlté tlačidlo na zmenu hesla (ikonka zámok s kruhom).')
            ->line('5. Zadajte Vaše nové heslo dvakrát.')
            ->line('6. Stlačte "Upraviť" a pokiaľ ste zadali správne dvakrát rovnaké heslo, Vaše heslo je zmenené.')

            ->line('----------------------------------------------------------')

            ->line('Postup vypožičania knihy je veľmi jednoduchý:')
            ->line('1. Prihláste sa pomocou Vašich prihlasovacích údajov.')
            ->line('2. Vyhľadajte si knihu, ktorú si chcete vypožičať.')
            ->line('3. Ak je kniha dostupná, stlačte sivé tlačidlo na rezervovanie knihy.')
            ->line('4. Do siedmych dní sa zastavte u nás v knižnici a prevezmite si knihu.')

            ->line('----------------------------------------------------------')

            ->line('V prípade akýchkoľvek otázok, neváhajte nás kontaktovať na mailovú adresu: kniznica@kusjanakollara.org')


            ->action('Vstup do knižnice', url('https://kniznica.kusjanakollara.org/'))

            ->line('Ďakujeme a tešíme sa spolu s Vami!')
            ->salutation('S pozdravom, tím knižnice KUS Jána Kollára');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
