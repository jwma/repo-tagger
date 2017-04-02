<?php

namespace AppBundle\Command;

use AppBundle\Entity\Repo;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCrawlStarredListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:crawl-starred-list')
            ->setDescription('爬去 star 的仓库列表');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crawlUrl = 'https://api.github.com/users/jwma/starred';

        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $client = new Client();

        $username = $container->getParameter('github_username');
        $password = $container->getParameter('github_password');

        do {
            $response = $client->request('get', $crawlUrl, [
                'auth' => [$username, $password]
            ]);

            $data = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
            foreach ($data as $one) {
                $checkRepo = $em->getRepository('AppBundle:Repo')->findOneBy(['htmlUrl' => $one['html_url']]);
                if ($checkRepo) {
                    $output->writeln($checkRepo->getName() . ' 已录入，跳过...');
                    continue;
                }

                $repo = new Repo();
                $repo
                    ->setName($one['name'])
                    ->setFullName($one['full_name'])
                    ->setHtmlUrl($one['html_url'])
                    ->setDescription($one['description'])
                    ->setTags([])
                    ->setCreatedAt(new \DateTime($one['created_at']));

                $output->writeln('保存 ' . $repo->getName());
                $em->persist($repo);
            }
            $em->flush();

            if (!$response->hasHeader('Link')) {
                break;
            }

            $link = $response->getHeader('Link')[0];

            $urlType = trim(explode(';', explode(',', $link)[0])[1]);
            $url = explode(';', explode(',', $link)[0])[0];
            $url = str_replace('<', '', $url);
            $url = str_replace('>', '', $url);

            if ($urlType != 'rel="next"') {
                break;
            }

            $crawlUrl = $url;

            $output->writeln('准备爬取下一页数据...');
            sleep(1);
        } while (true);

        $output->writeln('爬取完毕');
    }
}
