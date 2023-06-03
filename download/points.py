from requests import Session
from math import ceil
from time import sleep

session = Session()
articles = []
url = 'https://www.wikidata.org/w/api.php'

with open('articles.tsv', 'r', encoding='utf-8') as f:
    for line in f:
        articles.append(line.strip().split('\t')[0].replace('_', ' '))

batchCount = ceil(len(articles) / 50)

with open('loaded.txt', 'a', encoding='utf-8') as f:
    for i in range(0, len(articles), 50):
        batch = articles[i:i+50]
        print(f'Batch {i // 50 + 1} of {batchCount}')

        params = {
            'format': 'json',
            'formatversion': 2,
            'action': 'wbgetentities',
            'props': 'sitelinks|claims|descriptions|labels',
            'languages': 'pl',
            'sitefilter': 'plwiki',
            'sites': 'plwiki',
            'titles': '|'.join(batch)
        }
        r = session.get(url, params=params)
        data = r.json()

        entities = data['entities']
        for qid, entity in entities.items():
            try:
                if 'missing' in entity:
                    f.write(f'{entity["title"]}\t!!MISSING!!\t\t\t\t\n')
                    continue

                plwiki = entity['sitelinks']['plwiki']['title']

                description = ''
                if 'pl' in entity['descriptions']:
                    description = entity['descriptions']['pl']['value']

                label = ''
                if 'pl' in entity['labels']:
                    label = entity['labels']['pl']['value']

                coords = ('', '')
                if 'P625' in entity['claims']:
                    p625 = entity['claims']['P625']
                    snak = p625[0]['mainsnak']
                    if snak['snaktype'] == 'value':
                        location = snak['datavalue']['value']
                        coords = (location['latitude'], location['longitude'])

                f.write(f'{plwiki}\t{qid}\t{coords[0]}\t{coords[1]}\t{label}\t{description}\n')
            except Exception as e:
                f.write(f'{plwiki}\t!!ERROR!! {e}\t\t\t\t\n')
                print(f'{plwiki}: Error: {e}')
        sleep(1)
